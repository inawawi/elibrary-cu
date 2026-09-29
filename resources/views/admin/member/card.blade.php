<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kartu Anggota - {{ $member->member_name }}</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            body { background: white !important; padding: 0 !important; }
            .no-print { display: none !important; }
            .card-wrapper { box-shadow: none !important; margin: 0 !important; }
        }
    </style>
</head>
<body class="bg-slate-100 min-h-screen py-10 px-4 font-['Plus_Jakarta_Sans',sans-serif]">

    <div class="max-w-xl mx-auto space-y-6">
        <!-- Print Toolbar -->
        <div class="flex items-center justify-between no-print bg-white p-4 rounded-2xl shadow-sm border border-slate-200">
            <span class="text-xs font-bold text-slate-700">Preview Kartu Anggota Perpustakaan</span>
            <div class="flex items-center gap-2">
                <button onclick="window.close()" class="px-4 py-2 rounded-xl text-xs font-bold text-slate-600 hover:bg-slate-100">Tutup</button>
                <button onclick="window.print()" class="px-5 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold shadow-md">Cetak Kartu</button>
            </div>
        </div>

        <!-- FRONT CARD -->
        <div class="card-wrapper w-[480px] h-[280px] mx-auto rounded-3xl p-6 bg-gradient-to-tr from-sky-900 via-blue-900 to-indigo-950 text-white shadow-2xl relative overflow-hidden border border-white/10 flex flex-col justify-between">
            <div class="absolute -right-12 -top-12 w-48 h-48 rounded-full bg-white/10 blur-2xl pointer-events-none"></div>
            <div class="absolute -left-12 -bottom-12 w-48 h-48 rounded-full bg-sky-500/20 blur-2xl pointer-events-none"></div>

            <!-- Header -->
            <div class="flex items-center justify-between relative">
                <div class="flex items-center gap-3">
                    <img src="{{ asset('images/logo.png') }}" alt="Logo Universitas Siber Indonesia" class="w-10 h-10 object-contain">
                    <div>
                        <div class="text-[11px] font-extrabold uppercase tracking-widest text-sky-300">KARTU ANGGOTA PERPUSTAKAAN</div>
                        <div class="text-xs font-black tracking-tight">UNIVERSITAS SIBER INDONESIA</div>
                    </div>
                </div>
                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-white/20 backdrop-blur">
                    {{ $member->memberType?->member_type_name ?: 'Mahasiswa' }}
                </span>
            </div>

            <!-- Body -->
            <div class="flex items-center gap-5 relative">
                <img src="{{ $member->avatar_url }}" class="w-20 h-24 rounded-2xl object-cover border-2 border-white/40 shadow-lg">
                <div class="min-w-0">
                    <div class="text-base font-black tracking-tight leading-snug line-clamp-1">{{ $member->member_name }}</div>
                    <div class="font-mono text-xs text-sky-200 font-bold tracking-wider mt-0.5">{{ $member->member_id }}</div>
                    <div class="text-[11px] text-white/80 mt-1 line-clamp-1">{{ $member->inst_name ?: 'Universitas Siber Indonesia' }}</div>
                    <div class="text-[10px] text-white/60 mt-0.5">{{ $member->member_email ?: '-' }}</div>
                </div>
            </div>

            <!-- Footer -->
            <div class="pt-3 border-t border-white/15 flex items-center justify-between text-[10px] relative">
                <div>
                    <span class="text-white/60 block text-[9px] uppercase tracking-wider">Berlaku Hingga</span>
                    <span class="font-bold">{{ $member->isLecturer() ? 'Selama Bertugas' : ($member->expire_date ? \Carbon\Carbon::parse($member->expire_date)->format('d/m/Y') : 'Seumur Hidup') }}</span>
                </div>
                <div class="text-right">
                    <span class="text-white/60 block text-[9px] uppercase tracking-wider">Kontak Surel Resmi</span>
                    <span class="font-bold text-sky-200">perpustakaan@cyber-univ.ac.id</span>
                </div>
            </div>
        </div>

        <!-- BACK CARD -->
        <div class="card-wrapper w-[480px] h-[280px] mx-auto rounded-3xl p-6 bg-slate-900 text-white shadow-2xl relative overflow-hidden border border-slate-700 flex flex-col justify-between">
            <div>
                <h4 class="text-xs font-bold text-sky-400 uppercase tracking-wider mb-2">Ketentuan Penggunaan Kartu</h4>
                <ol class="text-[10px] text-slate-300 space-y-1 list-decimal list-inside leading-relaxed">
                    <li>Kartu ini adalah bukti keanggotaan sah Perpustakaan Universitas Siber Indonesia.</li>
                    <li>Kartu tidak dapat dipindahtangankan kepada orang lain.</li>
                    <li>Wajib dibawa setiap kali berkunjung dan melakukan peminjaman buku.</li>
                    <li>Kehilangan kartu harap segera dilaporkan ke meja layanan sirkulasi.</li>
                    <li>Buku yang terlambat dikembalikan dikenakan denda sesuai peraturan berlaku.</li>
                </ol>
            </div>

            <div class="pt-4 border-t border-slate-800 flex items-center justify-between">
                <div>
                    <div class="font-mono text-sm tracking-widest text-slate-400 font-bold">*{{ $member->member_id }}*</div>
                    <div class="text-[9px] text-slate-500">DIGITAL LIBRARY PASS</div>
                </div>
                <div class="text-right">
                    <div class="text-[10px] font-bold text-slate-300">Kepala Perpustakaan</div>
                    <div class="text-[9px] text-slate-500 mt-4">Universitas Siber Indonesia</div>
                </div>
            </div>
        </div>
    </div>

</body>
</html>
