<x-mazer-layout :pageTitle="'Kelola Users'">
    <x-slot>

        <header class="mb-3">
            <a href="#" class="burger-btn d-block d-xl-none">
                <i class="bi bi-justify fs-3"></i>
            </a>
        </header>

        <div class="page-heading">
            <h3>Kelola Users</h3>
        </div> 

        <section class="section">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-md-6 col-12">
                            <h4 class="card-title">Data Pengguna</h4>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    {{-- Menampilkan Notifikasi Sukses --}}
                    @if (session('status'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('status') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    {{-- Menampilkan Notifikasi Error --}}
                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <h6>Terdapat beberapa error:</h6>
                            <ul>
                                @foreach ($errors->all() as $e)
                                    <li>{{ $e }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
                    
                    {{-- Form Pencarian --}}
                    <div class="my-3">
                        <form method="GET" action="{{ route('admin.users.index') }}">
                            <div class="input-group">
                                <input type="search" class="form-control" name="q" value="{{ $q ?? request('q') }}" placeholder="Cari berdasarkan nama atau email..." />
                                <button class="btn btn-primary" type="submit">
                                    <i class="bi bi-search me-2"></i>Cari
                                </button>
                                <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">Reset</a>
                            </div>
                        </form>
                    </div>
                    
                    {{-- Tabel Data --}}
                    <div class="table-responsive">
                        <table class="table table-striped table-hover table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 5%;">ID</th>
                                    <th>Nama</th>
                                    <th>Email</th>
                                    <th>Role</th>
                                    <th style="width: 35%;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($users as $u)
                                    <tr>
                                        <td>{{ $u->id }}</td>
                                        <td>{{ $u->name }}</td>
                                        <td>{{ $u->email }}</td>
                                        <td>
                                            {{-- Menggunakan Badge untuk Role --}}
                                            @if ($u->roleString() === 'admin')
                                                <span class="badge bg-primary">Admin</span>
                                            @elseif ($u->roleString() === 'user')
                                                <span class="badge bg-secondary">User</span>
                                            @else
                                                <span class="badge bg-info">{{ $u->roleString() }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="d-flex gap-2 flex-wrap">

                                                @can('changeRole', $u)
                                                    <form method="POST" action="{{ route('admin.users.changeRole', $u) }}" class="d-inline-flex">
                                                        @csrf
                                                        @method('PATCH')
                                                        <div class="input-group input-group-sm">
                                                            <select name="role" class="form-select">
                                                                <option value="admin" @selected($u->roleString()==='admin')>admin</option>
                                                                <option value="user"  @selected($u->roleString()==='user')>user</option>
                                                                <option value="other" @selected($u->roleString()==='other')>other</option>
                                                            </select>
                                                            <button type="submit" class="btn btn-sm btn-info">
                                                                <i class="bi bi-arrow-repeat"></i> Ubah
                                                            </button>
                                                        </div>
                                                    </form>
                                                @endcan
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center">Belum ada data pengguna.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Paginasi --}}
                    <div class="mt-4">
                        {{ $users->onEachSide(1)->links() }}
                    </div>
                </div>
            </div>
        </section>

    </x-slot>
</x-mazer-layout>