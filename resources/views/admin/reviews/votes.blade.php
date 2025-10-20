<x-mazer-layout>
  <x-slot>

    <header class="mb-3">
        <a href="#" class="burger-btn d-block d-xl-none">
            <i class="bi bi-justify fs-3"></i>
        </a>
    </header>

    <h2>Votes for Review #{{ $review->id }}</h2>

    @if (session('status')) <div>{{ session('status') }}</div> @endif

    <div style="margin-bottom:12px; display:flex; gap:8px; flex-wrap:wrap;">
      <a href="{{ route('admin.reviews.index') }}">← Kembali ke Reviews</a>
      <a href="{{ route('admin.reviews.edit', $review) }}">Edit Review</a>
      <span>Produk: <strong>{{ $review->product?->name ?? '—' }}</strong></span>
    </div>

    <table border="1" cellpadding="6" cellspacing="0" width="100%">
      <thead>
        <tr>
          <th style="text-align:left;">ID</th>
          <th style="text-align:left;">User</th>
          <th style="text-align:left;">Vote</th>
          <th style="text-align:left;">Waktu</th>
          <th style="text-align:left;">Aksi</th>
        </tr>
      </thead>
      <tbody>
        @forelse($votes as $v)
          <tr>
            <td>#{{ $v->id }}</td>
            <td>{{ $v->user?->name ?? '—' }} <small>(id: {{ $v->user_id }})</small></td>
            <td>{{ $v->vote }}</td>
            <td>{{ $v->created_at }}</td>
            <td>
              <form method="POST" action="{{ route('admin.reviews.votes.destroy', [$review, $v]) }}"
                    onsubmit="return confirm('Hapus vote ini?');">
                @csrf @method('DELETE')
                <button type="submit">Hapus</button>
              </form>
            </td>
          </tr>
        @empty
          <tr><td colspan="5">Belum ada vote.</td></tr>
        @endforelse
      </tbody>
    </table>

    <div style="margin-top:12px;">
      {{ $votes->onEachSide(1)->links() }}
    </div>

  </x-slot>
</x-mazer-layout>
