<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;
use Carbon\Carbon;

class ProductAdminController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', Product::class);

        $q            = (string) $request->string('q');
        $categoryId   = $request->integer('category_id') ?: null;
        $onlyActive   = $request->boolean('only_active', false);
        $onlyFeatured = $request->boolean('only_featured', false);

        $categoryOptions = $this->buildCategoryOptions();

        $products = Product::query()
            ->with(['category:id,name', 'primaryImage'])
            ->withCount('images')
            ->when($q, function ($query) use ($q) {
                $query->where(function ($qq) use ($q) {
                    $qq->where('name', 'like', "%{$q}%")
                       ->orWhere('slug', 'like', "%{$q}%")
                       ->orWhere('sku', 'like', "%{$q}%")
                       ->orWhere('short_desc', 'like', "%{$q}%");
                });
            })
            ->when($categoryId !== null, function ($query) use ($categoryId) {
                $query->where('category_id', $categoryId);
            })
            ->when($onlyActive, fn($q) => $q->where('is_active', true))
            ->when($onlyFeatured, fn($q) => $q->where('is_featured', true))
            ->orderByDesc('id')
            ->paginate(12)
            ->withQueryString();

        return view('admin.products.index', [
            'products'        => $products,
            'categoryOptions' => $categoryOptions,
            'q'               => $q,
            'categoryId'      => $categoryId,
            'onlyActive'      => $onlyActive,
            'onlyFeatured'    => $onlyFeatured,
        ]);
    }

    public function create()
    {
        $this->authorize('create', Product::class);
        $categoryOptions = $this->buildCategoryOptions();

        return view('admin.products.create', [
            'categoryOptions' => $categoryOptions,
        ]);
    }

    public function store(Request $request)
    {
        $this->authorize('create', Product::class);

        $validated = $request->validate([
            'category_id'       => ['required', 'integer', 'exists:categories,id'],
            'name'              => ['required', 'string', 'max:255'],
            'slug'              => [
                'nullable', 'string', 'max:255',
                Rule::unique('products', 'slug')->whereNull('deleted_at'),
            ],
            'sku'               => [
                'nullable', 'string', 'max:100',
                Rule::unique('products', 'sku')->whereNull('deleted_at'),
            ],
            'short_desc'        => ['nullable', 'string'],
            'long_desc'         => ['nullable', 'string'],
            'original_price'    => ['required', 'integer', 'min:0'],
            'discount_percent'  => ['nullable', 'integer', 'min:0', 'max:100'],
            'stock'             => ['nullable', 'integer', 'min:0'],
            'weight'            => ['nullable', 'integer', 'min:0'],
            'published_at'      => ['nullable', 'date'],
            // flags
            'share_fb'          => ['nullable', 'boolean'],
            'share_x'           => ['nullable', 'boolean'],
            'share_wa'          => ['nullable', 'boolean'],
            'is_best_seller'    => ['nullable', 'boolean'],
            'is_new'            => ['nullable', 'boolean'],
            'is_hot'            => ['nullable', 'boolean'],
            'is_featured'       => ['nullable', 'boolean'],
            'is_active'         => ['nullable', 'boolean'],
        ]);

        // slug aman & unik (menghormati soft deletes)
        $slug = $validated['slug'] ?? Str::slug($validated['name']);
        $base = $slug;
        $i = 1;
        while (Product::withTrashed()->where('slug', $slug)->exists()) {
            $slug = $base.'-'.$i++;
        }

        $payload = [
            'category_id'      => $validated['category_id'],
            'name'             => $validated['name'],
            'slug'             => $slug,
            'sku'              => $validated['sku'] ?? null,
            'short_desc'       => $validated['short_desc'] ?? null,
            'long_desc'        => $validated['long_desc'] ?? null,
            'original_price'   => $validated['original_price'],
            'discount_percent' => $validated['discount_percent'] ?? 0,
            'stock'            => $validated['stock'] ?? 0,
            'weight'           => $validated['weight'] ?? 0,
            'share_fb'         => (bool) ($validated['share_fb'] ?? false),
            'share_x'          => (bool) ($validated['share_x'] ?? false),
            'share_wa'         => (bool) ($validated['share_wa'] ?? false),
            'is_best_seller'   => (bool) ($validated['is_best_seller'] ?? false),
            'is_new'           => (bool) ($validated['is_new'] ?? false),
            'is_hot'           => (bool) ($validated['is_hot'] ?? false),
            'is_featured'      => (bool) ($validated['is_featured'] ?? false),
            'is_active'        => (bool) ($validated['is_active'] ?? false),
            'published_at'     => !empty($validated['published_at'])
                                    ? Carbon::parse($validated['published_at'])
                                    : null,
        ];

        $product = Product::create($payload);

        return redirect()
            ->route('admin.products.edit', $product)
            ->with('status', 'Product created');
    }

    public function edit(Product $product)
    {
        $this->authorize('update', $product);
        $categoryOptions = $this->buildCategoryOptions();

        return view('admin.products.edit', [
            'product'         => $product,
            'categoryOptions' => $categoryOptions,
        ]);
    }

    public function update(Request $request, Product $product)
    {
        $this->authorize('update', $product);

        $validated = $request->validate([
            'category_id'       => ['required', 'integer', 'exists:categories,id'],
            'name'              => ['required', 'string', 'max:255'],
            'slug'              => [
                'nullable', 'string', 'max:255',
                Rule::unique('products', 'slug')->ignore($product->id)->whereNull('deleted_at'),
            ],
            'sku'               => [
                'nullable', 'string', 'max:100',
                Rule::unique('products', 'sku')->ignore($product->id)->whereNull('deleted_at'),
            ],
            'short_desc'        => ['nullable', 'string'],
            'long_desc'         => ['nullable', 'string'],
            'original_price'    => ['required', 'integer', 'min:0'],
            'discount_percent'  => ['nullable', 'integer', 'min:0', 'max:100'],
            'stock'             => ['nullable', 'integer', 'min:0'],
            'weight'            => ['nullable', 'integer', 'min:0'],
            'published_at'      => ['nullable', 'date'],
            // flags
            'share_fb'          => ['nullable', 'boolean'],
            'share_x'           => ['nullable', 'boolean'],
            'share_wa'          => ['nullable', 'boolean'],
            'is_best_seller'    => ['nullable', 'boolean'],
            'is_new'            => ['nullable', 'boolean'],
            'is_hot'            => ['nullable', 'boolean'],
            'is_featured'       => ['nullable', 'boolean'],
            'is_active'         => ['nullable', 'boolean'],
        ]);

        $payload = [
            'category_id'      => $validated['category_id'],
            'name'             => $validated['name'],
            'slug'             => $validated['slug'] ? Str::slug($validated['slug']) : Str::slug($validated['name']),
            'sku'              => $validated['sku'] ?? null,
            'short_desc'       => $validated['short_desc'] ?? null,
            'long_desc'        => $validated['long_desc'] ?? null,
            'original_price'   => $validated['original_price'],
            'discount_percent' => $validated['discount_percent'] ?? 0,
            'stock'            => $validated['stock'] ?? 0,
            'weight'           => $validated['weight'] ?? 0,
            'share_fb'         => (bool) ($validated['share_fb'] ?? false),
            'share_x'          => (bool) ($validated['share_x'] ?? false),
            'share_wa'         => (bool) ($validated['share_wa'] ?? false),
            'is_best_seller'   => (bool) ($validated['is_best_seller'] ?? false),
            'is_new'           => (bool) ($validated['is_new'] ?? false),
            'is_hot'           => (bool) ($validated['is_hot'] ?? false),
            'is_featured'      => (bool) ($validated['is_featured'] ?? false),
            'is_active'        => (bool) ($validated['is_active'] ?? false),
            'published_at'     => !empty($validated['published_at'])
                                    ? Carbon::parse($validated['published_at'])
                                    : null,
        ];

        $product->update($payload);

        return redirect()
            ->route('admin.products.edit', $product)
            ->with('status', 'Product updated');
    }

    /**
     * Toggle boolean field yang diizinkan.
     */
    public function toggle(Product $product, string $field)
    {
        $this->authorize('update', $product);

        $allowed = [
            'is_active', 'is_featured', 'is_best_seller', 'is_new', 'is_hot',
            'share_fb', 'share_x', 'share_wa',
        ];

        if (! in_array($field, $allowed, true)) {
            abort(404);
        }

        $product->{$field} = ! (bool) $product->{$field};
        $product->save();

        return back()->with('status', "Toggled {$field}");
    }

    public function destroy(Product $product)
    {
        $this->authorize('delete', $product);
        $product->delete();

        return redirect()
            ->route('admin.products.index')
            ->with('status', 'Product deleted');
    }

    /** ---------- Helpers ---------- */

    /**
     * Build opsi kategori nested
     * @return array<int, array{id:int,label:string}>
     */
    private function buildCategoryOptions(): array
    {
        $roots = Category::query()
            ->select('id', 'name', 'parent_id', 'sort_order')
            ->whereNull('parent_id')
            ->orderBy('sort_order')->orderBy('name')
            ->get();

        $options = [];
        foreach ($roots as $root) {
            $this->pushCategoryOption($options, $root, 0);
        }
        return $options;
    }

    private function pushCategoryOption(array &$options, Category $cat, int $depth)
    {
        $options[] = [
            'id'    => $cat->id,
            'label' => str_repeat('— ', $depth) . $cat->name,
        ];

        $children = $cat->children()
            ->select('id', 'name', 'parent_id', 'sort_order')
            ->orderBy('sort_order')->orderBy('name')->get();

        foreach ($children as $ch) {
            $this->pushCategoryOption($options, $ch, $depth + 1);
        }
    }
}
