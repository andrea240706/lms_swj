/**
 * Utilitas Ekspor Dokumen Resmi Sekolah (Real Functional Downloads)
 * Menghasilkan unduhan berkas nyata untuk format CSV, Excel, dan PDF Siap Cetak.
 */

export function downloadCSV(filename, headers, rows) {
    const csvContent = [
        headers.join(','),
        ...rows.map((row) =>
            row.map((cell) => `"${String(cell ?? '').replace(/"/g, '""')}"`).join(',')
        ),
    ].join('\r\n');

    const blob = new Blob(['\uFEFF' + csvContent], { type: 'text/csv;charset=utf-8;' });
    triggerDownload(blob, filename.endsWith('.csv') ? filename : `${filename}.csv`);
}

export function downloadExcel(filename, title, headers, rows) {
    const tableHtml = `
    <html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">
    <head>
        <!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>${title}</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]-->
        <meta http-equiv="content-type" content="text/plain; charset=UTF-8"/>
        <style>
            th { background-color: #2D3748; color: #FFFFFF; font-weight: bold; border: 1px solid #CBD5E0; padding: 8px; text-align: left; }
            td { border: 1px solid #E2E8F0; padding: 6px; }
            .title { font-size: 16pt; font-weight: bold; margin-bottom: 8px; }
            .meta { font-size: 10pt; color: #4A5568; margin-bottom: 12px; }
        </style>
    </head>
    <body>
        <div class="title">${title}</div>
        <div class="meta">SMKN 1 NUSANTARA • Tahun Ajaran 2026/2027 Ganjil • Tanggal Cetak: ${new Date().toLocaleDateString('id-ID')}</div>
        <table>
            <thead>
                <tr>${headers.map((h) => `<th>${h}</th>`).join('')}</tr>
            </thead>
            <tbody>
                ${rows.map((r) => `<tr>${r.map((c) => `<td>${c ?? ''}</td>`).join('')}</tr>`).join('')}
            </tbody>
        </table>
    </body>
    </html>`;

    const blob = new Blob(['\uFEFF' + tableHtml], { type: 'application/vnd.ms-excel;charset=utf-8;' });
    triggerDownload(blob, filename.endsWith('.xls') || filename.endsWith('.xlsx') ? filename : `${filename}.xls`);
}

export function downloadPDF(title, subtitle, headers, rows, summaryNotes = []) {
    const printWindow = window.open('', '_blank');
    if (!printWindow) {
        alert('Izinkan pop-up peramban Anda untuk mencetak atau mengunduh dokumen laporan PDF resmi.');
        return;
    }

    const html = `
    <!DOCTYPE html>
    <html lang="id">
    <head>
        <meta charset="UTF-8">
        <title>${title}</title>
        <style>
            @page { size: A4 landscape; margin: 12mm; }
            body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; color: #1a202c; margin: 0; padding: 20px; font-size: 10pt; }
            .header-kop { border-bottom: 2px solid #1a202c; padding-bottom: 12px; margin-bottom: 18px; display: flex; justify-content: space-between; align-items: center; }
            .school-name { font-size: 16pt; font-weight: 800; letter-spacing: 0.5px; text-transform: uppercase; }
            .school-sub { font-size: 9pt; color: #4a5568; margin-top: 2px; }
            .doc-title { font-size: 13pt; font-weight: bold; margin-top: 10px; text-transform: uppercase; text-align: center; }
            .doc-sub { font-size: 9.5pt; color: #4a5568; text-align: center; margin-bottom: 16px; }
            table { width: 100%; border-collapse: collapse; margin-top: 10px; font-size: 9.5pt; }
            th { background-color: #2d3748; color: white; border: 1px solid #2d3748; padding: 8px 10px; text-align: left; }
            td { border: 1px solid #cbd5e0; padding: 6px 10px; }
            tr:nth-child(even) { background-color: #f7fafc; }
            .notes { margin-top: 18px; font-size: 9pt; color: #4a5568; }
            .signatures { margin-top: 35px; display: flex; justify-content: space-between; }
            .sig-block { text-align: center; width: 220px; font-size: 9.5pt; }
            .sig-space { height: 55px; }
            .sig-name { font-weight: bold; text-decoration: underline; }
            .no-print-bar { background: #edf2f7; padding: 12px; text-align: center; margin-bottom: 20px; border-radius: 8px; border: 1px solid #cbd5e0; }
            .print-btn { background: #2b6cb0; color: white; border: none; padding: 8px 18px; font-weight: bold; border-radius: 6px; cursor: pointer; font-size: 11pt; }
            @media print { .no-print-bar { display: none; } }
        </style>
    </head>
    <body>
        <div class="no-print-bar">
            <button class="print-btn" onclick="window.print()">🖨️ Cetak / Simpan sebagai PDF</button>
            <span style="margin-left: 15px; font-size: 9.5pt; color: #4a5568;">Pilih printer <strong>"Save as PDF"</strong> pada dialog print untuk menyimpan berkas PDF rapi.</span>
        </div>
        <div class="header-kop">
            <div>
                <div class="school-name">SMK NEGERI 1 NUSANTARA</div>
                <div class="school-sub">Akreditasi A • Bidang Keahlian: Teknologi Informasi, Seni Kreatif, & Bisnis Manajemen</div>
            </div>
            <div style="text-align: right; font-size: 9pt; color: #4a5568;">
                <div>Tahun Ajaran 2026/2027 • Ganjil</div>
                <div>Status Dokumen: <strong>RESMI LAPORAN EKSEKUTIF</strong></div>
            </div>
        </div>
        <div class="doc-title">${title}</div>
        <div class="doc-sub">${subtitle}</div>
        <table>
            <thead>
                <tr>${headers.map((h) => `<th>${h}</th>`).join('')}</tr>
            </thead>
            <tbody>
                ${rows.map((r) => `<tr>${r.map((c) => `<td>${c ?? ''}</td>`).join('')}</tr>`).join('')}
            </tbody>
        </table>
        ${
            summaryNotes.length
                ? `<div class="notes"><strong>Catatan & Evaluasi Supervisi:</strong><ul>${summaryNotes.map((n) => `<li>${n}</li>`).join('')}</ul></div>`
                : ''
        }
        <div class="signatures">
            <div class="sig-block">
                <div>Mengetahui,</div>
                <div>Wakil Kepala Sekolah Kurikulum</div>
                <div class="sig-space"></div>
                <div class="sig-name">Ahmad Fauzi, M.Pd</div>
                <div>NIP. 19780512 200312 1 002</div>
            </div>
            <div class="sig-block">
                <div>Kota Bandung, ${new Date().toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' })}</div>
                <div>Kepala Sekolah SMKN 1 Nusantara</div>
                <div class="sig-space"></div>
                <div class="sig-name">Drs. Hendra Wijaya</div>
                <div>NIP. 19680315 199403 1 004</div>
            </div>
        </div>
    </body>
    </html>`;

    printWindow.document.open();
    printWindow.document.write(html);
    printWindow.document.close();
}

function triggerDownload(blob, filename) {
    const link = document.createElement('a');
    const url = URL.createObjectURL(blob);
    link.href = url;
    link.download = filename;
    document.body.appendChild(link);
    link.click();
    setTimeout(() => {
        document.body.removeChild(link);
        URL.revokeObjectURL(url);
    }, 150);
}
