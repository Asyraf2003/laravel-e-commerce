<x-mazer-layout>
    {{-- Slot untuk header halaman, sesuai standar Mazer --}}
    <x-slot name="header">
        <div class="page-heading">
            <h3>Profil Pengguna</h3>
            <p class="text-subtitle text-muted">Kelola informasi akun, kata sandi, dan data profil Anda.</p>
        </div>
    </x-slot>

    <div class="page-content">
        <section class="section">
            <div class="row">
                {{-- Kolom utama untuk form yang sering digunakan --}}
                <div class="col-12 col-lg-8">
                    {{-- Bagian Informasi Profil --}}
                    <div class="mb-4">
                        @include('profile.partials.update-profile-information-form')
                    </div>

                    {{-- Bagian Ubah Kata Sandi --}}
                    <div>
                        @include('profile.partials.update-password-form')
                    </div>
                </div>

                {{-- Kolom samping untuk tindakan destruktif/sekunder --}}
                <div class="col-12 col-lg-4">
                    {{-- Bagian Hapus Akun --}}
                    <div>
                        @include('profile.partials.delete-user-form')
                    </div>
                </div>
            </div>
        </section>
    </div>
</x-mazer-layout>