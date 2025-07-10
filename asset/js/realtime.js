// Ambil variabel dari PHP yang sudah kita buat di footer.php
const role = typeof loggedInUserRole !== 'undefined' ? loggedInUserRole : null;
const userId = typeof loggedInUserId !== 'undefined' ? loggedInUserId : null;

// Hanya jalankan kode jika user sudah login dan rolenya terdefinisi
if (role && userId) {
    // Aktifkan log di console browser untuk debugging
    Pusher.logToConsole = true;

    const pusher = new Pusher('287ce2af6d82f8141418', {
      cluster: 'ap1'
    });

    // =================================================
    // LOGIKA JIKA YANG LOGIN ADALAH KARYAWAN (USER)
    // =================================================
    if (role === 'user') {
        const userChannel = pusher.subscribe('user-channel-' + userId);

        userChannel.bind('status-update', function(data) {
            console.log('Event status-update diterima untuk user:', data);

            // Update kartu statistik di dashboard
            const countDisetujuiEl = document.getElementById('count-disetujui');
            if (countDisetujuiEl) countDisetujuiEl.innerText = data.count_disetujui;

            const countDitolakEl = document.getElementById('count-ditolak');
            if (countDitolakEl) countDitolakEl.innerText = data.count_ditolak;

            const countMenungguEl = document.getElementById('count-menunggu');
            if (countMenungguEl) countMenungguEl.innerText = data.count_menunggu;

            // Update status di tabel (baik di dashboard atau riwayat)
            const statusBadge = document.getElementById('status-badge-' + data.id_pengajuan);
            if (statusBadge) {
                statusBadge.innerText = data.status_baru;
                statusBadge.className = 'badge'; // Reset kelas
                if (data.status_baru === 'Disetujui') {
                    statusBadge.classList.add('bg-success');
                } else if (data.status_baru === 'Ditolak') {
                    statusBadge.classList.add('bg-danger');
                }
            }
            
            // Update tombol aksi di halaman riwayat
            const rowAksi = document.querySelector('#cuti-row-' + data.id_pengajuan + ' .text-center a');
            if (rowAksi && data.status_baru !== 'Diajukan') {
                rowAksi.parentElement.innerHTML = '-'; // Ganti tombol 'Batalkan' dengan strip
            }
        });
    }

    // =================================================
    // LOGIKA JIKA YANG LOGIN ADALAH ADMIN
    // =================================================
    if (role === 'admin') {
        const adminChannel = pusher.subscribe('admin-channel');

        adminChannel.bind('pengajuan-baru', function(data) {
            console.log('Event pengajuan-baru diterima untuk admin:', data);

            // Update kartu statistik di dashboard admin
            const stats = data.statistik_baru;
            document.getElementById('cuti-bulan-ini').innerText = stats.cuti_bulan_ini;
            document.getElementById('menunggu-konfirmasi').innerText = stats.cuti_diajukan;

            // Tambah baris baru ke tabel pengajuan terbaru
            const tabelBody = document.getElementById('tabel-cuti-terbaru');
            if (tabelBody) {
                // Hapus pesan "Belum ada pengajuan cuti" jika ada
                const barisKosong = document.getElementById('baris-kosong');
                if (barisKosong) {
                    barisKosong.remove();
                }

                const pengajuan = data.pengajuan_baru;
                const tglMulai = new Date(pengajuan.tanggal_mulai).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' });
                const tglSelesai = new Date(pengajuan.tanggal_selesai).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' });
                
                const newRow = `
                    <tr class="table-row-animate" style="background-color: #fff8e1;">
                        <td>Baru!</td>
                        <td>${pengajuan.nama_lengkap}</td>
                        <td>${tglMulai}</td>
                        <td>${tglSelesai}</td>
                        <td><span class="badge bg-warning text-dark">${pengajuan.status}</span></td>
                    </tr>
                `;
                // Masukkan baris baru di paling atas tabel
                tabelBody.insertAdjacentHTML('afterbegin', newRow);
            }
        });
    }
}