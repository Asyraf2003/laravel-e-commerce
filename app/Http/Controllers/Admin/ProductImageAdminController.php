<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProductImageAdminController extends Controller
{
    public function index(Product $product)
    {
        $this->authorize('update', $product);

        $images = $product->images()->ordered()->get();

        return view('admin.product_images.index', [
            'product' => $product,
            'images'  => $images,
        ]);
    }

    public function store(Request $request, Product $product)
    {
        $this->authorize('update', $product);

        $validated = $request->validate([
            'images'   => ['required', 'array', 'min:1'],
            'images.*' => ['image', 'max:4096'], // 4MB per file
        ]);

        $next = (int) ($product->images()->max('sort_order') ?? 0);

        DB::transaction(function () use ($product, $validated, &$next) {
            foreach ($validated['images'] as $i => $file) {
                // Simpan ke disk public (php artisan storage:link wajib sudah)
                $path = $file->store('products/'.$product->id, 'public'); // simpan relatif: products/{id}/...
                $img = new ProductImage([
                    'image_path' => $path,           // simpan path relatif (tanpa 'storage/')
                    'sort_order' => ++$next,
                    'alt_text'   => $product->name,  // default, bisa diedit
                ]);
                $product->images()->save($img);

                // Set primary otomatis jika belum ada sama sekali
                if (! $product->primaryImage()->exists()) {
                    $img->is_primary = true;
                    $img->save();
                }
            }
        });

        return back()->with('status', 'Gambar berhasil diunggah');
    }

    public function update(Request $request, Product $product, ProductImage $image)
    {
        $this->authorize('update', $product);
        $this->assertBelongsTo($product, $image);

        $validated = $request->validate([
            'alt_text'   => ['nullable', 'string', 'max:255'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_primary' => ['nullable', 'boolean'],
        ]);

        // Update field dasar
        $image->alt_text   = $validated['alt_text'] ?? $image->alt_text;
        $image->sort_order = $validated['sort_order'] ?? $image->sort_order;
        $image->save();

        // Opsi: set primary via checkbox
        if ($request->has('is_primary')) {
            return $this->makePrimary($product, $image);
        }

        return back()->with('status', 'Gambar diperbarui');
    }

    public function makePrimary(Product $product, ProductImage $image)
    {
        $this->authorize('update', $product);
        $this->assertBelongsTo($product, $image);

        DB::transaction(function () use ($product, $image) {
            $product->images()->update(['is_primary' => false]);
            $image->is_primary = true;
            $image->save();
        });

        return back()->with('status', 'Primary image diubah');
    }

    public function reorder(Request $request, Product $product)
    {
        $this->authorize('update', $product);

        $data = $request->validate([
            'orders'   => ['required', 'array'],
            'orders.*' => ['integer', 'min:0'], // key = image_id, val = sort_order
        ]);

        DB::transaction(function () use ($product, $data) {
            // Pastikan semua id milik product
            $ids = array_keys($data['orders']);
            $owned = $product->images()->whereIn('id', $ids)->pluck('id')->all();

            foreach ($ids as $id) {
                if (! in_array($id, $owned, true)) {
                    abort(403, 'Invalid image id for this product');
                }
                ProductImage::whereKey($id)->update(['sort_order' => (int) $data['orders'][$id]]);
            }
        });

        return back()->with('status', 'Urutan gambar diperbarui');
    }

    public function destroy(Product $product, ProductImage $image)
    {
        $this->authorize('update', $product);
        $this->assertBelongsTo($product, $image);

        DB::transaction(function () use ($product, $image) {
            // Hapus file
            if ($image->image_path) {
                Storage::disk('public')->delete($image->image_path); // karena kita simpan path relatif
            }

            $wasPrimary = $image->is_primary;
            $image->delete();

            // Jika yang dihapus adalah primary, pindahkan primary ke gambar dengan sort_order terkecil
            if ($wasPrimary) {
                $next = $product->images()->ordered()->first();
                if ($next) {
                    $next->is_primary = true;
                    $next->save();
                }
            }
        });

        return back()->with('status', 'Gambar dihapus');
    }

    /** ------- Helpers ------- */
    private function assertBelongsTo(Product $product, ProductImage $image): void
    {
        if ($image->product_id !== $product->id) {
            abort(404);
        }
    }
}
