<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class CategoryAdminController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', Category::class);

        $q         = (string) $request->string('q');
        $parentId  = $request->integer('parent_id') ?: null;
        $onlyAct   = $request->boolean('only_active', false);

        $parents = Category::query()
            ->select('id', 'name')
            ->orderBy('name')
            ->get();

        $categories = Category::query()
            ->withCount([
                'children as children_count',
                'products as products_count',
            ])
            ->with(['parent:id,name'])
            ->when($q, function ($query) use ($q) {
                $query->where(function ($qq) use ($q) {
                    $qq->where('name', 'like', "%{$q}%")
                       ->orWhere('slug', 'like', "%{$q}%")
                       ->orWhere('description', 'like', "%{$q}%");
                });
            })
            ->when($parentId !== null, function ($query) use ($parentId) {
                $query->where('parent_id', $parentId);
            })
            ->when($onlyAct, fn($query) => $query->where('is_active', true))
            ->orderBy('sort_order')
            ->orderBy('name')
            ->paginate(12)
            ->withQueryString();

        return view('admin.categories.index', [
            'categories' => $categories,
            'parents'    => $parents,
            'q'          => $q,
            'parentId'   => $parentId,
            'onlyAct'    => $onlyAct,
        ]);
    }

    public function create()
    {
        $this->authorize('create', Category::class);

        $parentOptions = $this->buildParentOptions();
        return view('admin.categories.create', [
            'parentOptions' => $parentOptions,
        ]);
    }

    public function store(Request $request)
    {
        $this->authorize('create', Category::class);

        $validated = $request->validate([
            'name'        => ['required', 'string', 'max:255'],
            'slug'        => [
                'nullable', 'string', 'max:255',
                Rule::unique('categories', 'slug')->whereNull('deleted_at'),
            ],
            'parent_id'   => ['nullable', 'integer', 'exists:categories,id'],
            'description' => ['nullable', 'string'],
            'image'       => ['nullable', 'image', 'max:2048'],
            'is_active'   => ['nullable', 'boolean'],
            'sort_order'  => ['nullable', 'integer', 'min:0'],
        ]);

        $slug = $validated['slug'] ?? Str::slug($validated['name']);

        // Pastikan slug unik (fallback tambahkan suffix angka)
        $base = $slug;
        $i = 1;
        while (Category::withTrashed()->where('slug', $slug)->exists()) {
            $slug = $base.'-'.$i++;
        }

        $payload = [
            'name'        => $validated['name'],
            'slug'        => $slug,
            'parent_id'   => $validated['parent_id'] ?? null,
            'description' => $validated['description'] ?? null,
            'is_active'   => (bool) ($validated['is_active'] ?? false),
            'sort_order'  => $validated['sort_order'] ?? 0,
        ];

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('categories', 'public');
            $payload['image'] = $path; // simpan relative path (storage disk public)
        }

        $category = Category::create($payload);

        return redirect()
            ->route('admin.categories.edit', $category)
            ->with('status', 'Category created');
    }

    public function edit(Category $category)
    {
        $this->authorize('update', $category);

        $excludeIds    = array_merge([$category->id], $this->getDescendantIds($category));
        $parentOptions = $this->buildParentOptions($excludeIds);

        return view('admin.categories.edit', [
            'category'      => $category,
            'parentOptions' => $parentOptions,
        ]);
    }

    public function update(Request $request, Category $category)
    {
        $this->authorize('update', $category);

        $validated = $request->validate([
            'name'        => ['required', 'string', 'max:255'],
            'slug'        => [
                'nullable', 'string', 'max:255',
                Rule::unique('categories', 'slug')
                    ->ignore($category->id)
                    ->whereNull('deleted_at'),
            ],
            'parent_id'   => ['nullable', 'integer', 'exists:categories,id'],
            'description' => ['nullable', 'string'],
            'image'       => ['nullable', 'image', 'max:2048'],
            'is_active'   => ['nullable', 'boolean'],
            'sort_order'  => ['nullable', 'integer', 'min:0'],
        ]);

        // Cegah parent jadi anak sendiri/descendant
        if (!empty($validated['parent_id'])) {
            $descendants = $this->getDescendantIds($category);
            if (in_array((int) $validated['parent_id'], array_merge([$category->id], $descendants), true)) {
                return back()
                    ->withErrors(['parent_id' => 'Parent tidak boleh diri sendiri atau turunannya.'])
                    ->withInput();
            }
        }

        $payload = [
            'name'        => $validated['name'],
            'slug'        => $validated['slug'] ? Str::slug($validated['slug']) : Str::slug($validated['name']),
            'parent_id'   => $validated['parent_id'] ?? null,
            'description' => $validated['description'] ?? null,
            'is_active'   => (bool) ($validated['is_active'] ?? false),
            'sort_order'  => $validated['sort_order'] ?? 0,
        ];

        if ($request->hasFile('image')) {
            if ($category->image) {
                Storage::disk('public')->delete($category->image);
            }
            $path = $request->file('image')->store('categories', 'public');
            $payload['image'] = $path;
        }

        $category->update($payload);

        return redirect()
            ->route('admin.categories.edit', $category)
            ->with('status', 'Category updated');
    }

    public function toggleActive(Category $category)
    {
        $this->authorize('update', $category);
        $category->is_active = ! $category->is_active;
        $category->save();

        return back()->with('status', 'Status updated');
    }

    public function destroy(Category $category)
    {
        $this->authorize('delete', $category);
        $category->delete();

        return redirect()
            ->route('admin.categories.index')
            ->with('status', 'Category deleted');
    }

    /** -------- Helpers -------- */

    /**
     * Buat opsi parent (nested) sebagai array [id, label, disabled]
     * @param array<int> $excludeIds
     * @return array<int, array{id:int,label:string,disabled:bool}>
     */
    private function buildParentOptions(array $excludeIds = []): array
    {
        $roots = Category::query()
            ->select('id', 'name', 'parent_id', 'sort_order')
            ->whereNull('parent_id')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        $options = [];
        foreach ($roots as $root) {
            $this->pushOptionRecursive($options, $root, 0, $excludeIds);
        }
        return $options;
    }

    private function pushOptionRecursive(array &$options, Category $cat, int $depth, array $excludeIds)
    {
        $options[] = [
            'id'       => $cat->id,
            'label'    => str_repeat('— ', $depth) . $cat->name,
            'disabled' => in_array($cat->id, $excludeIds, true),
        ];

        $children = $cat->children()
            ->select('id', 'name', 'parent_id', 'sort_order')
            ->orderBy('sort_order')->orderBy('name')->get();

        foreach ($children as $ch) {
            $this->pushOptionRecursive($options, $ch, $depth + 1, $excludeIds);
        }
    }

    /**
     * Ambil semua descendant id (rekursif).
     * @return array<int>
     */
    private function getDescendantIds(Category $category): array
    {
        $ids = [];
        $children = $category->children()->select('id')->get();
        foreach ($children as $child) {
            $ids[] = $child->id;
            $ids   = array_merge($ids, $this->getDescendantIds($child));
        }
        return $ids;
    }
}
