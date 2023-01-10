<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Rab;
use App\Models\lokasi;
use App\Models\Prk;
use App\Models\ItemRincianInduk;
use App\Models\Skk;
use App\Models\Khs;
use App\Models\KontrakInduk;
use App\Models\RincianInduk;
use App\Models\Pejabat;
use App\Models\Addendum;
use App\Models\Vendor;
// use App\Models\OrderedRab;
use App\Models\OrderKhs;
use App\Models\OrderPaket;
use App\Models\OrderRedaksiKHS;
use App\Models\Redaksi;
use App\Models\SubRedaksi;
use App\Models\RabRedaksi;
use App\Models\Satuan;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Barryvdh\DomPDF\PDF as DomPDFPDF;
use DateInterval;
use DatePeriod;
use Illuminate\Support\Carbon;
use PhpParser\Node\Expr\Cast\Double;
use Riskihajar\Terbilang\Facades\Terbilang;
use DateTime;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Str;

use Webklex\PDFMerger\Facades\PDFMergerFacade as PDFMerger;


class PdfkhsController extends Controller
{
    // CETAK TKDN NON PAKET PDF
    public function cetak_tkdn_lampiran(Request $request)
    {


        // dd($request);
        // $request->validate([
        //     'nomor_po' => 'required|unique:rabs|max:250',
        //     'tanggal_po' => 'required|max:250',
        //     'skk_id' => 'required|max:250',
        //     'prk_id' => 'required|max:250',
        //     'pekerjaan' => 'required|max:250',
        //     'lokasi' => 'nullable|max:250',
        //     'startdate' => 'required|max:250',
        //     'enddate' => 'required|max:250',
        //     'nomor_kontrak_induk' => 'required|max:250',
        //     'addendum_id' => 'nullable|max:250',
        //     'pejabat_id' => 'required|max:250',
        //     'pengawas' => 'required|max:250',
        //     'total_harga' => 'required|max:250',
        //     'kategori_order' => 'required|max:250',
        //     'item_order' => 'required|max:250',
        //     'satuan_id' => 'required|max:250',
        //     'harga_satuan' => 'required|max:250',
        //     'volume' => 'required|max:250',
        //     'jumlah_harga' => 'required|max:250',
        //     // 'jumlah_harga' => 'required',
        // ]);



        $addendum_id = Addendum::where('nomor_addendum', $request->addendum_id)->value('id');

        $nama_pdf = $request->nomor_po;
        $nama_pdf = str_replace('.', '_', $nama_pdf);
        $nama_pdf = str_replace('/','-', $nama_pdf);
        $nama_pdf = str_replace(' ','-', $nama_pdf);

        // dd($nama_pdf);


        $mypdf = 'storage/storage/file-pdf-khs/tkdn/'.$nama_pdf.'.pdf';

        $rab = [
            'nomor_po' => $request->nomor_po,
            'tanggal_po' => $request->tanggal_po,
            'skk_id' => $request->skk_id,
            'prk_id' => $request->prk_id,
            'pekerjaan' => $request->pekerjaan,
            // 'lokasi' => $request->lokasi,
            'startdate' => $request->startdate,
            'enddate' => $request->enddate,
            'nomor_kontrak_induk' => $request->nomor_kontrak_induk,
            'addendum_id' => $addendum_id,
            'pejabat_id' => $request->pejabat_id,
            'pengawas_pekerjaan' => $request->pengawas_pekerjaan,
            'total_harga' => $request->total_harga,
            'pdf_file' =>$mypdf,
        ];

        Rab::create($rab);
        // dd($rab);

        $id = Rab::where('nomor_po', $request->nomor_po)->value('id');

        $total_tabel = $request->click;


        $rab_id = [];
        $satuan_id = [];
        $nama_item_id = [];

        for ($i = 0; $i < $total_tabel; $i++) {
            $rab_id[$i] = $id;
            $satuan_id[$i] = Satuan::where('kepanjangan', $request->satuan_id[$i])->value('id');
            $nama_item_id[$i] = RincianINduk::where('nama_item', $request->item_order[$i])->value('id');
        }



        for ($j = 0; $j < $total_tabel; $j++) {
            $order_khs = [
                'rab_id' => $rab_id[$j],
                'kategori_order' => $request->kategori_order[$j],
                'item_order' => $nama_item_id[$j],
                'satuan_id' => $satuan_id[$j],
                'harga_satuan' => $request->harga_satuan[$j],
                'volume' => $request->volume[$j],
                'jumlah_harga' => $request->jumlah_harga[$j],
                'tkdn' => $request->tkdn[$j],
                'kdn' => $request->kdn[$j],
                'kln' => $request->kln[$j],
                'total_tkdn' => $request->total_tkdn[$j],
            ];
            OrderKhs::create($order_khs);
        }


        $redaksi_click = $request->clickredaksi;
        for ($i = 0; $i < $redaksi_click; $i++) {
            $rab_id[$i] = $id;
        }
        // $rab_redaksi = [];
        $subdeksripsi_id = [];

        $console = $request->sub_deskripsi_id;

        // dd($console);


        for ($i = 0; $i < count($request->sub_deskripsi_id); $i++) {
            // $subdeksripsi_id[$i] = SubRedaksi::where('sub_deskripsi', $request->sub_deskripsi_id[$i])->where('redaksi_id', $request->redaksi_id)->value('id'),
            $sub_deskripsi_id = SubRedaksi::where('sub_deskripsi', $request->sub_deskripsi_id[$i])->where('redaksi_id', $request->redaksi_id)->value('id');
            $rab_redaksi = [
                "rab_id" => $id,
                "subdeskripsi_id" => $sub_deskripsi_id
            ];
            RabRedaksi::create($rab_redaksi);
        }

        // $sub_deskripsi_id = $request->sub_deskripsi_id;
        // $redaksi_id = $request->redaksi_id;
        // $deskripsi_id = $request->deskripsi_id;

        dd($rab_redaksi);

        for ($i = 0; $i < count($sub_deskripsi_id); $i++) {
            $sub_deskripsi_id[$i] = RabRedaksi ::where('sub_deskripsi', $sub_deskripsi_id)->value('id');
        }




        for ($j = 0; $j < $redaksi_click; $j++) {
            $order_redaksi = [
                'rab_id' => $rab_id[$j],
                'redaksi_id' => $redaksi_id[$j],
                'deskripsi_id' => $deskripsi_id[$j],
                'sub_deskripsi_id' => $sub_deskripsi_id[$j],
            ];
            OrderRedaksiKHS::create($order_redaksi);
        }

        $lokasi_click = $request->clicklokasi;
        for ($i = 0; $i < $lokasi_click; $i++) {
            $rab_id[$i] = $id;
        }
        for ($j = 0; $j < $lokasi_click; $j++) {
            $order_lokasi = [
                'rab_id' => $rab_id[$j],
                'nama_lokasi' => $request->lokasi[$j],

            ];
            lokasi::create($order_lokasi);
        }



        $redaksis = OrderRedaksiKHS::where('rab_id', $rab_id)->get();
        $lokasis = lokasi::where('rab_id', $rab_id)->get();
        // dd($redaksis);

        $values_pdf_page1 = Rab::where('id', $id)->get();

        $startdate = Rab::where('id', $id)->value('startdate');
        $enddate = Rab::where('id', $id)->value('enddate');
        $datetime1 = new DateTime($startdate);
        $datetime2 = new DateTime($enddate);
        $interval = new DatePeriod($datetime1, new DateInterval('P1D'), $datetime2);
        $d = 0;
        $days = 0;
        $datetime2 = 1;

        foreach($interval as $date) {
            $interval = $date->format("Y-m-d");
            $datetime = DateTime::createFromFormat('Y-m-d', $interval);

            $day = $datetime->format('D');

            if($day != "Sun" && $day != "Sat") {
                $days += $datetime2 - $d;
            }

            $datetime2++;
            $d++;
        }

        $rab_id = Rab::where('id', $id)->value('id');
        $values_pdf_page2 = OrderKhs::where('rab_id', $rab_id)->get();

        $jasa = [];
        $jasa_volume = [];
        $ubah_volume_jasa = [];
        $material = [];
        $material_volume = [];
        $ubah_volume_material = [];

        for($i = 0; $i < count($values_pdf_page2); $i++) {
            if($values_pdf_page2[$i]->kategori_order == "Jasa") {
                $jasa[$i] = $values_pdf_page2[$i];
                $jasa_volume[$i] = $jasa[$i]->volume;
                $ubah_volume_jasa[$i] = str_replace(".", ",", "$jasa_volume[$i]");
                $jasa[$i]->volume = $ubah_volume_jasa[$i];
                // $jasa[$i]->volume = str_replace()
            } else {
                $material[$i] = $values_pdf_page2[$i];
                $material_volume[$i] = $material[$i]->volume;
                $ubah_volume_material[$i] = str_replace(".", ",", "$material_volume[$i]");
                $material[$i]->volume = $ubah_volume_material[$i];
            }
        }
        // dd($jasa);
        // $jabatan = Pejabat::select('jabatan');

        $jabatan_manager = Pejabat::where('jabatan', 'Manager UP3')->value('jabatan');
        $nama_manager = Pejabat::where('jabatan', 'Manager UP3')->value('nama_pejabat');

        $jumlah = OrderKhs::where('rab_id', $rab_id)->sum('jumlah_harga');
        $ppn = $jumlah * 0.11;


    $pdf = Pdf::loadView('format_surat.redaksi_jtm',[
            "po_khs" => $values_pdf_page1,
            "kategori_jasa" => $jasa,
            "kategori_material" => $material,
            "jumlah" => $jumlah,
            "ppn" => $ppn,
            "days" => $days,
            "jabatan_manager" => $jabatan_manager,
            "nama_manager" => $nama_manager,
            "title" => $nama_pdf,
            "redaksis" => $redaksis,
            "lokasis" => $lokasis,
        ]);

        $path1 = 'SPBJ.pdf';
        Storage::disk('local')->put($path1, $pdf->output());

        $pdf2 = Pdf::loadView('format_surat.rab_tkdn',[
            "po_khs" => $values_pdf_page1,
            "kategori_jasa" => $jasa,
            "kategori_material" => $material,
            "jumlah" => $jumlah,
            "ppn" => $ppn,
            "days" => $days,
            "jabatan_manager" => $jabatan_manager,
            "nama_manager" => $nama_manager,
            "title" => $nama_pdf,
            "redaksis" => $redaksis,
            "lokasis" => $lokasis,
        ]);

        // $content = $pdf->download()->getOriginalContent();
        // Storage::put('public/storage/file-pdf-khs/'.$nama_pdf.'.pdf',$content);
        $pdf2->setPaper('A4', 'landscape');
        $path2 = 'RAB.pdf';
        Storage::disk('local')->put($path2, $pdf2->output());

        $oMerger = PDFMerger::init();
        $oMerger->addPDF(Storage::disk('local')->path($path1), 'all');
        $oMerger->addPDF(Storage::disk('local')->path($path2), 'all');
        $oMerger->addPDF($request->file('lampiran')->getPathName(), 'all');

        $oMerger->merge();
        $oMerger->save('storage/storage/file-pdf-khs/tkdn/'.$nama_pdf.'.pdf');

        $updated_prk_terkontrak = 0;
        $previous_prk_terkontrak = Rab::where('prk_id', $request->prk_id)->get('total_harga');
        foreach ($previous_prk_terkontrak as $prk_terkontrak)
            $updated_prk_terkontrak += (float)$prk_terkontrak->total_harga;
        Prk::where('id', $request->prk_id)->update(array('prk_terkontrak' => (float)$updated_prk_terkontrak));

        //Update PRK Sisa
        $pagu_prk = Prk::where('id', $request->prk_id)->value('pagu_prk');
        $prk_terkontrak = Prk::where('id', $request->prk_id)->value('prk_terkontrak');
        $updated_prk_sisa = (float)$pagu_prk - (float)$prk_terkontrak;
        Prk::where('id', $request->prk_id)->update(array('prk_sisa' => (float)$updated_prk_sisa));

        //Update SKK Terkontrak
        $updated_skk_terkontrak = 0;
        $previous_skk_terkontrak = Prk::where('no_skk_prk', $request->skk_id)->get('prk_terkontrak');
        foreach ($previous_skk_terkontrak as $skk_terkontrak)
            $updated_skk_terkontrak += (float)$skk_terkontrak->prk_terkontrak;
        Skk::where('id', $request->skk_id)->update(array('skk_terkontrak' => (float)$updated_skk_terkontrak));

        //Update SKK Sisa
        $pagu_skk = Skk::where('id', $request->skk_id)->value('pagu_skk');
        $skk_terkontrak = Skk::where('id', $request->skk_id)->value('skk_terkontrak');
        $updated_skk_sisa = (float)$pagu_skk - (float)$skk_terkontrak;
        Skk::where('id', $request->skk_id)->update(array('skk_sisa' => (float)$updated_skk_sisa));
        // $id = compact('id');
        return response()->json($id);
    }


    public function cetak_tkdn_non_lampiran(Request $request)
    {
        // dd($request);
        // $request->validate([
        //     'nomor_po' => 'required|unique:rabs|max:250',
        //     'tanggal_po' => 'required|max:250',
        //     'skk_id' => 'required|max:250',
        //     'prk_id' => 'required|max:250',
        //     'pekerjaan' => 'required|max:250',
        //     'lokasi' => 'nullable|max:250',
        //     'startdate' => 'required|max:250',
        //     'enddate' => 'required|max:250',
        //     'nomor_kontrak_induk' => 'required|max:250',
        //     'addendum_id' => 'nullable|max:250',
        //     'pejabat_id' => 'required|max:250',
        //     'pengawas' => 'required|max:250',
        //     'total_harga' => 'required|max:250',
        //     'kategori_order' => 'required|max:250',
        //     'item_order' => 'required|max:250',
        //     'satuan_id' => 'required|max:250',
        //     'harga_satuan' => 'required|max:250',
        //     'volume' => 'required|max:250',
        //     'jumlah_harga' => 'required|max:250',
        //     // 'jumlah_harga' => 'required',
        // ]);



        $addendum_id = Addendum::where('nomor_addendum', $request->addendum_id)->value('id');

        $nama_pdf = $request->nomor_po;
        $nama_pdf = str_replace('.', '_', $nama_pdf);
        $nama_pdf = str_replace('/','-', $nama_pdf);
        $nama_pdf = str_replace(' ','-', $nama_pdf);

        // dd($nama_pdf);


        $mypdf = 'storage/storage/file-pdf-khs/tkdn/'.$nama_pdf.'.pdf';

        $rab = [
            'nomor_po' => $request->nomor_po,
            'tanggal_po' => $request->tanggal_po,
            'skk_id' => $request->skk_id,
            'prk_id' => $request->prk_id,
            'pekerjaan' => $request->pekerjaan,
            // 'lokasi' => $request->lokasi,
            'startdate' => $request->startdate,
            'enddate' => $request->enddate,
            'nomor_kontrak_induk' => $request->nomor_kontrak_induk,
            'addendum_id' => $addendum_id,
            'pejabat_id' => $request->pejabat_id,
            'pengawas_pekerjaan' => $request->pengawas_pekerjaan,
            'total_harga' => $request->total_harga,
            'pdf_file' =>$mypdf,
        ];

        Rab::create($rab);
        // dd($rab);

        $id = Rab::where('nomor_po', $request->nomor_po)->value('id');

        $total_tabel = $request->click;


        $rab_id = [];
        $satuan_id = [];
        $nama_item_id = [];

        for ($i = 0; $i < $total_tabel; $i++) {
            $rab_id[$i] = $id;
            $satuan_id[$i] = Satuan::where('kepanjangan', $request->satuan_id[$i])->value('id');
            $nama_item_id[$i] = RincianINduk::where('nama_item', $request->item_order[$i])->value('id');
        }



        for ($j = 0; $j < $total_tabel; $j++) {
            $order_khs = [
                'rab_id' => $rab_id[$j],
                'kategori_order' => $request->kategori_order[$j],
                'item_order' => $nama_item_id[$j],
                'satuan_id' => $satuan_id[$j],
                'harga_satuan' => $request->harga_satuan[$j],
                'volume' => $request->volume[$j],
                'jumlah_harga' => $request->jumlah_harga[$j],
            ];
            OrderKhs::create($order_khs);
        }


        $redaksi_click = $request->clickredaksi;
        for ($i = 0; $i < $redaksi_click; $i++) {
            $rab_id[$i] = $id;
        }
        for ($j = 0; $j < $redaksi_click; $j++) {
            $order_redaksi = [
                'rab_id' => $rab_id[$j],
                'redaksi_id' => $request->redaksi_id[$j],
                'deskripsi_id' => $request->deskripsi_id[$j],
                'sub_deskripsi_id' => $request->sub_deskripsi_id[$j],
            ];
            OrderRedaksiKHS::create($order_redaksi);
        }

        $lokasi_click = $request->clicklokasi;
        for ($i = 0; $i < $lokasi_click; $i++) {
            $rab_id[$i] = $id;
        }
        for ($j = 0; $j < $lokasi_click; $j++) {
            $order_lokasi = [
                'rab_id' => $rab_id[$j],
                'nama_lokasi' => $request->lokasi[$j],

            ];
            lokasi::create($order_lokasi);
        }



        $redaksis = OrderRedaksiKHS::where('rab_id', $rab_id)->get();
        $lokasis = lokasi::where('rab_id', $rab_id)->get();
        // dd($redaksis);

        $values_pdf_page1 = Rab::where('id', $id)->get();

        $startdate = Rab::where('id', $id)->value('startdate');
        $enddate = Rab::where('id', $id)->value('enddate');
        $datetime1 = new DateTime($startdate);
        $datetime2 = new DateTime($enddate);
        $interval = new DatePeriod($datetime1, new DateInterval('P1D'), $datetime2);
        $d = 0;
        $days = 0;
        $datetime2 = 1;

        foreach($interval as $date) {
            $interval = $date->format("Y-m-d");
            $datetime = DateTime::createFromFormat('Y-m-d', $interval);

            $day = $datetime->format('D');

            if($day != "Sun" && $day != "Sat") {
                $days += $datetime2 - $d;
            }

            $datetime2++;
            $d++;
        }

        $rab_id = Rab::where('id', $id)->value('id');
        $values_pdf_page2 = OrderKhs::where('rab_id', $rab_id)->get();

        $jasa = [];
        $jasa_volume = [];
        $ubah_volume_jasa = [];
        $material = [];
        $material_volume = [];
        $ubah_volume_material = [];

        for($i = 0; $i < count($values_pdf_page2); $i++) {
            if($values_pdf_page2[$i]->kategori_order == "Jasa") {
                $jasa[$i] = $values_pdf_page2[$i];
                $jasa_volume[$i] = $jasa[$i]->volume;
                $ubah_volume_jasa[$i] = str_replace(".", ",", "$jasa_volume[$i]");
                $jasa[$i]->volume = $ubah_volume_jasa[$i];
                // $jasa[$i]->volume = str_replace()
            } else {
                $material[$i] = $values_pdf_page2[$i];
                $material_volume[$i] = $material[$i]->volume;
                $ubah_volume_material[$i] = str_replace(".", ",", "$material_volume[$i]");
                $material[$i]->volume = $ubah_volume_material[$i];
            }
        }
        // dd($jasa);
        // $jabatan = Pejabat::select('jabatan');

        $jabatan_manager = Pejabat::where('jabatan', 'Manager UP3')->value('jabatan');
        $nama_manager = Pejabat::where('jabatan', 'Manager UP3')->value('nama_pejabat');

        $jumlah = OrderKhs::where('rab_id', $rab_id)->sum('jumlah_harga');
        $ppn = $jumlah * 0.11;


        $pdf = Pdf::loadView('layouts.surat',[
            "po_khs" => $values_pdf_page1,
            "kategori_jasa" => $jasa,
            "kategori_material" => $material,
            "jumlah" => $jumlah,
            "ppn" => $ppn,
            "days" => $days,
            "jabatan_manager" => $jabatan_manager,
            "nama_manager" => $nama_manager,
            "title" => $nama_pdf,
            "redaksis" => $redaksis,
            "lokasis" => $lokasis,
        ]);

        $path1 = 'SPBJ.pdf';
        Storage::disk('local')->put($path1, $pdf->output());

        $pdf2 = Pdf::loadView('layouts.surat_jtm',[
            "po_khs" => $values_pdf_page1,
            "kategori_jasa" => $jasa,
            "kategori_material" => $material,
            "jumlah" => $jumlah,
            "ppn" => $ppn,
            "days" => $days,
            "jabatan_manager" => $jabatan_manager,
            "nama_manager" => $nama_manager,
            "title" => $nama_pdf,
            "redaksis" => $redaksis,
            "lokasis" => $lokasis,
        ]);

        // $content = $pdf->download()->getOriginalContent();
        // Storage::put('public/storage/file-pdf-khs/'.$nama_pdf.'.pdf',$content);
        $pdf2->setPaper('A4', 'landscape');
        $path2 = 'RAB.pdf';
        Storage::disk('local')->put($path2, $pdf2->output());

        $oMerger = PDFMerger::init();
        $oMerger->addPDF(Storage::disk('local')->path($path1), 'all');
        $oMerger->addPDF(Storage::disk('local')->path($path2), 'all');

        $oMerger->merge();
        $oMerger->save('storage/storage/file-pdf-khs/tkdn/'.$nama_pdf.'.pdf');

        $updated_prk_terkontrak = 0;
        $previous_prk_terkontrak = Rab::where('prk_id', $request->prk_id)->get('total_harga');
        foreach ($previous_prk_terkontrak as $prk_terkontrak)
            $updated_prk_terkontrak += (float)$prk_terkontrak->total_harga;
        Prk::where('id', $request->prk_id)->update(array('prk_terkontrak' => (float)$updated_prk_terkontrak));

        //Update PRK Sisa
        $pagu_prk = Prk::where('id', $request->prk_id)->value('pagu_prk');
        $prk_terkontrak = Prk::where('id', $request->prk_id)->value('prk_terkontrak');
        $updated_prk_sisa = (float)$pagu_prk - (float)$prk_terkontrak;
        Prk::where('id', $request->prk_id)->update(array('prk_sisa' => (float)$updated_prk_sisa));

        //Update SKK Terkontrak
        $updated_skk_terkontrak = 0;
        $previous_skk_terkontrak = Prk::where('no_skk_prk', $request->skk_id)->get('prk_terkontrak');
        foreach ($previous_skk_terkontrak as $skk_terkontrak)
            $updated_skk_terkontrak += (float)$skk_terkontrak->prk_terkontrak;
        Skk::where('id', $request->skk_id)->update(array('skk_terkontrak' => (float)$updated_skk_terkontrak));

        //Update SKK Sisa
        $pagu_skk = Skk::where('id', $request->skk_id)->value('pagu_skk');
        $skk_terkontrak = Skk::where('id', $request->skk_id)->value('skk_terkontrak');
        $updated_skk_sisa = (float)$pagu_skk - (float)$skk_terkontrak;
        Skk::where('id', $request->skk_id)->update(array('skk_sisa' => (float)$updated_skk_sisa));
        // $id = compact('id');
        return response()->json($id);
    }


    //CETAK NON-TKDN NON-PAKET
    public function cetak_non_tkdn_lampiran(Request $request)
    {


        // dd($request);
        // $request->validate([
        //     'nomor_po' => 'required|unique:rabs|max:250',
        //     'tanggal_po' => 'required|max:250',
        //     'skk_id' => 'required|max:250',
        //     'prk_id' => 'required|max:250',
        //     'pekerjaan' => 'required|max:250',
        //     'lokasi' => 'nullable|max:250',
        //     'startdate' => 'required|max:250',
        //     'enddate' => 'required|max:250',
        //     'nomor_kontrak_induk' => 'required|max:250',
        //     'addendum_id' => 'nullable|max:250',
        //     'pejabat_id' => 'required|max:250',
        //     'pengawas' => 'required|max:250',
        //     'total_harga' => 'required|max:250',
        //     'kategori_order' => 'required|max:250',
        //     'item_order' => 'required|max:250',
        //     'satuan_id' => 'required|max:250',
        //     'harga_satuan' => 'required|max:250',
        //     'volume' => 'required|max:250',
        //     'jumlah_harga' => 'required|max:250',
        //     // 'jumlah_harga' => 'required',
        // ]);



        $addendum_id = Addendum::where('nomor_addendum', $request->addendum_id)->value('id');

        $nama_pdf = $request->nomor_po;
        $nama_pdf = str_replace('.', '_', $nama_pdf);
        $nama_pdf = str_replace('/','-', $nama_pdf);
        $nama_pdf = str_replace(' ','-', $nama_pdf);

        // dd($nama_pdf);


        $mypdf = 'storage/storage/file-pdf-khs/tkdn/'.$nama_pdf.'.pdf';

        $rab = [
            'nomor_po' => $request->nomor_po,
            'tanggal_po' => $request->tanggal_po,
            'skk_id' => $request->skk_id,
            'prk_id' => $request->prk_id,
            'pekerjaan' => $request->pekerjaan,
            // 'lokasi' => $request->lokasi,
            'startdate' => $request->startdate,
            'enddate' => $request->enddate,
            'nomor_kontrak_induk' => $request->nomor_kontrak_induk,
            'addendum_id' => $addendum_id,
            'pejabat_id' => $request->pejabat_id,
            'pengawas_pekerjaan' => $request->pengawas_pekerjaan,
            'total_harga' => $request->total_harga,
            'pdf_file' =>$mypdf,
        ];

        Rab::create($rab);
        // dd($rab);

        $id = Rab::where('nomor_po', $request->nomor_po)->value('id');

        $total_tabel = $request->click;


        $rab_id = [];
        $satuan_id = [];
        $nama_item_id = [];

        for ($i = 0; $i < $total_tabel; $i++) {
            $rab_id[$i] = $id;
            $satuan_id[$i] = Satuan::where('kepanjangan', $request->satuan_id[$i])->value('id');
            $nama_item_id[$i] = RincianINduk::where('nama_item', $request->item_order[$i])->value('id');
        }



        for ($j = 0; $j < $total_tabel; $j++) {
            $order_khs = [
                'rab_id' => $rab_id[$j],
                'kategori_order' => $request->kategori_order[$j],
                'item_order' => $nama_item_id[$j],
                'satuan_id' => $satuan_id[$j],
                'harga_satuan' => $request->harga_satuan[$j],
                'volume' => $request->volume[$j],
                'jumlah_harga' => $request->jumlah_harga[$j],
                'tkdn' => $request->tkdn[$j],
                'kdn' => $request->kdn[$j],
                'kln' => $request->kln[$j],
                'total_tkdn' => $request->total_tkdn[$j],
            ];
            OrderKhs::create($order_khs);
        }


        $redaksi_click = $request->clickredaksi;
        for ($i = 0; $i < $redaksi_click; $i++) {
            $rab_id[$i] = $id;
        }
        // $rab_redaksi = [];
        // $subdeksripsi_id = [];

        $console = $request->redaksi_id;

        // dd($request);
        // $array1 = [];

        // for ($i=0; $i < ; $i++) {
        //     # code...
        // }

        // $sub_deskripsi_id_array = [];

        // for ($i=0; $i <count($request->sub_deskripsi_id); $i++) {
        //     $sub_deskripsi_id_array = $request->sub_deskripsi_id;
        // }
        // dd($sub_deskripsi_id_array);


        for ($i = 0; $i < count($request->redaksi_id); $i++) {
            // $subdeksripsi_id[$i] = SubRedaksi::where('sub_deskripsi', $request->sub_deskripsi_id[$i])->where('redaksi_id', $request->redaksi_id)->value('id'),
            // $sub_deskripsi_id = SubRedaksi::where('sub_deskripsi', $request->sub_deskripsi_id[$i])->where('redaksi_id', $request->redaksi_id)->value('id');
            for ($j=0; $j <count($request->sub_deskripsi_id[$i]) ; $j++) {

                $rab_redaksi = [
                    "rab_id" => $id,
                    "subdeskripsi_id" => SubRedaksi::where('sub_deskripsi', $request->sub_deskripsi_id[$i][$j])->where('redaksi_id', $request->redaksi_id[$i])->value('id'),
                ];
                RabRedaksi::create($rab_redaksi);
            }
        }

        // $sub_deskripsi_id = $request->sub_deskripsi_id;
        // $redaksi_id = $request->redaksi_id;
        // $deskripsi_id = $request->deskripsi_id;

        // dd($rab_redaksi);






        for ($j = 0; $j < $redaksi_click; $j++) {
            $order_redaksi = [
                'rab_id' => $rab_id[$j],
                'redaksi_id' => $request->redaksi_id[$j],
                'deskripsi_id' => $request->deskripsi_id[$j],
                // 'sub_deskripsi_id' => $sub_deskripsi_id[$j],
            ];
            OrderRedaksiKHS::create($order_redaksi);
        }

        $lokasi_click = $request->clicklokasi;
        for ($i = 0; $i < $lokasi_click; $i++) {
            $rab_id[$i] = $id;
        }
        for ($j = 0; $j < $lokasi_click; $j++) {
            $order_lokasi = [
                'rab_id' => $rab_id[$j],
                'nama_lokasi' => $request->lokasi[$j],

            ];
            lokasi::create($order_lokasi);
        }



        $rabredaksi = RabRedaksi::where('rab_id', $rab_id)->get();

        for ($i=0; $i < count($rabredaksi) ; $i++) {
            $rabredaksi[$i];
        }



        $redaksis = OrderRedaksiKHS::where('rab_id', $rab_id)->get();
        $lokasis = lokasi::where('rab_id', $rab_id)->get();
        // dd($redaksis);

        $values_pdf_page1 = Rab::where('id', $id)->get();

        $startdate = Rab::where('id', $id)->value('startdate');
        $enddate = Rab::where('id', $id)->value('enddate');
        $datetime1 = new DateTime($startdate);
        $datetime2 = new DateTime($enddate);
        $interval = new DatePeriod($datetime1, new DateInterval('P1D'), $datetime2);
        $d = 0;
        $days = 0;
        $datetime2 = 1;

        foreach($interval as $date) {
            $interval = $date->format("Y-m-d");
            $datetime = DateTime::createFromFormat('Y-m-d', $interval);

            $day = $datetime->format('D');

            if($day != "Sun" && $day != "Sat") {
                $days += $datetime2 - $d;
            }

            $datetime2++;
            $d++;
        }

        $rab_id = Rab::where('id', $id)->value('id');
        $values_pdf_page2 = OrderKhs::where('rab_id', $rab_id)->get();

        $jasa = [];
        $jasa_volume = [];
        $ubah_volume_jasa = [];
        $material = [];
        $material_volume = [];
        $ubah_volume_material = [];

        for($i = 0; $i < count($values_pdf_page2); $i++) {
            if($values_pdf_page2[$i]->kategori_order == "Jasa") {
                $jasa[$i] = $values_pdf_page2[$i];
                $jasa_volume[$i] = $jasa[$i]->volume;
                $ubah_volume_jasa[$i] = str_replace(".", ",", "$jasa_volume[$i]");
                $jasa[$i]->volume = $ubah_volume_jasa[$i];
                // $jasa[$i]->volume = str_replace()
            } else {
                $material[$i] = $values_pdf_page2[$i];
                $material_volume[$i] = $material[$i]->volume;
                $ubah_volume_material[$i] = str_replace(".", ",", "$material_volume[$i]");
                $material[$i]->volume = $ubah_volume_material[$i];
            }
        }
        // dd($jasa);
        // $jabatan = Pejabat::select('jabatan');

        $jabatan_manager = Pejabat::where('jabatan', 'Manager UP3')->value('jabatan');
        $nama_manager = Pejabat::where('jabatan', 'Manager UP3')->value('nama_pejabat');

        $jumlah = OrderKhs::where('rab_id', $rab_id)->sum('jumlah_harga');
        $ppn = $jumlah * 0.11;


    $pdf = Pdf::loadView('format_surat.redaksi_spapp',[
            "po_khs" => $values_pdf_page1,
            "kategori_jasa" => $jasa,
            "kategori_material" => $material,
            "jumlah" => $jumlah,
            "ppn" => $ppn,
            "days" => $days,
            "jabatan_manager" => $jabatan_manager,
            "nama_manager" => $nama_manager,
            "title" => $nama_pdf,
            "redaksis" => $redaksis,
            "rabredaksi" => $rabredaksi,
            "lokasis" => $lokasis,
        ]);

        $path1 = 'SPBJ.pdf';
        Storage::disk('local')->put($path1, $pdf->output());

        $pdf2 = Pdf::loadView('format_surat.rab_non_tkdn',[
            "po_khs" => $values_pdf_page1,
            "kategori_jasa" => $jasa,
            "kategori_material" => $material,
            "jumlah" => $jumlah,
            "ppn" => $ppn,
            "days" => $days,
            "jabatan_manager" => $jabatan_manager,
            "nama_manager" => $nama_manager,
            "title" => $nama_pdf,
            "redaksis" => $redaksis,
            "lokasis" => $lokasis,
        ]);

        // $content = $pdf->download()->getOriginalContent();
        // Storage::put('public/storage/file-pdf-khs/'.$nama_pdf.'.pdf',$content);
        $path2 = 'RAB.pdf';
        Storage::disk('local')->put($path2, $pdf2->output());

        $oMerger = PDFMerger::init();
        $oMerger->addPDF(Storage::disk('local')->path($path1), 'all');
        $oMerger->addPDF(Storage::disk('local')->path($path2), 'all');
        $oMerger->addPDF($request->file('lampiran')->getPathName(), 'all');

        $oMerger->merge();
        $oMerger->save('storage/storage/file-pdf-khs/tkdn/'.$nama_pdf.'.pdf');

        $updated_prk_terkontrak = 0;
        $previous_prk_terkontrak = Rab::where('prk_id', $request->prk_id)->get('total_harga');
        foreach ($previous_prk_terkontrak as $prk_terkontrak)
            $updated_prk_terkontrak += (float)$prk_terkontrak->total_harga;
        Prk::where('id', $request->prk_id)->update(array('prk_terkontrak' => (float)$updated_prk_terkontrak));

        //Update PRK Sisa
        $pagu_prk = Prk::where('id', $request->prk_id)->value('pagu_prk');
        $prk_terkontrak = Prk::where('id', $request->prk_id)->value('prk_terkontrak');
        $updated_prk_sisa = (float)$pagu_prk - (float)$prk_terkontrak;
        Prk::where('id', $request->prk_id)->update(array('prk_sisa' => (float)$updated_prk_sisa));

        //Update SKK Terkontrak
        $updated_skk_terkontrak = 0;
        $previous_skk_terkontrak = Prk::where('no_skk_prk', $request->skk_id)->get('prk_terkontrak');
        foreach ($previous_skk_terkontrak as $skk_terkontrak)
            $updated_skk_terkontrak += (float)$skk_terkontrak->prk_terkontrak;
        Skk::where('id', $request->skk_id)->update(array('skk_terkontrak' => (float)$updated_skk_terkontrak));

        //Update SKK Sisa
        $pagu_skk = Skk::where('id', $request->skk_id)->value('pagu_skk');
        $skk_terkontrak = Skk::where('id', $request->skk_id)->value('skk_terkontrak');
        $updated_skk_sisa = (float)$pagu_skk - (float)$skk_terkontrak;
        Skk::where('id', $request->skk_id)->update(array('skk_sisa' => (float)$updated_skk_sisa));
        // $id = compact('id');
        return response()->json($id);
    }


    public function cetak_non_tkdn_non_lampiran(Request $request)
    {
        // dd($request);
        // $request->validate([
        //     'nomor_po' => 'required|unique:rabs|max:250',
        //     'tanggal_po' => 'required|max:250',
        //     'skk_id' => 'required|max:250',
        //     'prk_id' => 'required|max:250',
        //     'pekerjaan' => 'required|max:250',
        //     'lokasi' => 'nullable|max:250',
        //     'startdate' => 'required|max:250',
        //     'enddate' => 'required|max:250',
        //     'nomor_kontrak_induk' => 'required|max:250',
        //     'addendum_id' => 'nullable|max:250',
        //     'pejabat_id' => 'required|max:250',
        //     'pengawas' => 'required|max:250',
        //     'total_harga' => 'required|max:250',
        //     'kategori_order' => 'required|max:250',
        //     'item_order' => 'required|max:250',
        //     'satuan_id' => 'required|max:250',
        //     'harga_satuan' => 'required|max:250',
        //     'volume' => 'required|max:250',
        //     'jumlah_harga' => 'required|max:250',
        //     // 'jumlah_harga' => 'required',
        // ]);



        $addendum_id = Addendum::where('nomor_addendum', $request->addendum_id)->value('id');

        $nama_pdf = $request->nomor_po;
        $nama_pdf = str_replace('.', '_', $nama_pdf);
        $nama_pdf = str_replace('/','-', $nama_pdf);
        $nama_pdf = str_replace(' ','-', $nama_pdf);

        // dd($nama_pdf);


        $mypdf = 'storage/storage/file-pdf-khs/tkdn/'.$nama_pdf.'.pdf';

        $rab = [
            'nomor_po' => $request->nomor_po,
            'tanggal_po' => $request->tanggal_po,
            'skk_id' => $request->skk_id,
            'prk_id' => $request->prk_id,
            'pekerjaan' => $request->pekerjaan,
            // 'lokasi' => $request->lokasi,
            'startdate' => $request->startdate,
            'enddate' => $request->enddate,
            'nomor_kontrak_induk' => $request->nomor_kontrak_induk,
            'addendum_id' => $addendum_id,
            'pejabat_id' => $request->pejabat_id,
            'pengawas_pekerjaan' => $request->pengawas_pekerjaan,
            'total_harga' => $request->total_harga,
            'pdf_file' =>$mypdf,
        ];

        Rab::create($rab);
        // dd($rab);

        $id = Rab::where('nomor_po', $request->nomor_po)->value('id');

        $total_tabel = $request->click;


        $rab_id = [];
        $satuan_id = [];
        $nama_item_id = [];

        for ($i = 0; $i < $total_tabel; $i++) {
            $rab_id[$i] = $id;
            $satuan_id[$i] = Satuan::where('kepanjangan', $request->satuan_id[$i])->value('id');
            $nama_item_id[$i] = RincianINduk::where('nama_item', $request->item_order[$i])->value('id');
        }



        for ($j = 0; $j < $total_tabel; $j++) {
            $order_khs = [
                'rab_id' => $rab_id[$j],
                'kategori_order' => $request->kategori_order[$j],
                'item_order' => $nama_item_id[$j],
                'satuan_id' => $satuan_id[$j],
                'harga_satuan' => $request->harga_satuan[$j],
                'volume' => $request->volume[$j],
                'jumlah_harga' => $request->jumlah_harga[$j],
            ];
            OrderKhs::create($order_khs);
        }


        $redaksi_click = $request->clickredaksi;
        for ($i = 0; $i < $redaksi_click; $i++) {
            $rab_id[$i] = $id;
        }
        for ($j = 0; $j < $redaksi_click; $j++) {
            $order_redaksi = [
                'rab_id' => $rab_id[$j],
                'redaksi_id' => $request->redaksi_id[$j],
                'deskripsi_id' => $request->deskripsi_id[$j],
                'sub_deskripsi_id' => $request->sub_deskripsi_id[$j],
            ];
            OrderRedaksiKHS::create($order_redaksi);
        }

        $lokasi_click = $request->clicklokasi;
        for ($i = 0; $i < $lokasi_click; $i++) {
            $rab_id[$i] = $id;
        }
        for ($j = 0; $j < $lokasi_click; $j++) {
            $order_lokasi = [
                'rab_id' => $rab_id[$j],
                'nama_lokasi' => $request->lokasi[$j],

            ];
            lokasi::create($order_lokasi);
        }



        $redaksis = OrderRedaksiKHS::where('rab_id', $rab_id)->get();
        $lokasis = lokasi::where('rab_id', $rab_id)->get();
        // dd($redaksis);

        $values_pdf_page1 = Rab::where('id', $id)->get();

        $startdate = Rab::where('id', $id)->value('startdate');
        $enddate = Rab::where('id', $id)->value('enddate');
        $datetime1 = new DateTime($startdate);
        $datetime2 = new DateTime($enddate);
        $interval = new DatePeriod($datetime1, new DateInterval('P1D'), $datetime2);
        $d = 0;
        $days = 0;
        $datetime2 = 1;

        foreach($interval as $date) {
            $interval = $date->format("Y-m-d");
            $datetime = DateTime::createFromFormat('Y-m-d', $interval);

            $day = $datetime->format('D');

            if($day != "Sun" && $day != "Sat") {
                $days += $datetime2 - $d;
            }

            $datetime2++;
            $d++;
        }

        $rab_id = Rab::where('id', $id)->value('id');
        $values_pdf_page2 = OrderKhs::where('rab_id', $rab_id)->get();

        $jasa = [];
        $jasa_volume = [];
        $ubah_volume_jasa = [];
        $material = [];
        $material_volume = [];
        $ubah_volume_material = [];

        for($i = 0; $i < count($values_pdf_page2); $i++) {
            if($values_pdf_page2[$i]->kategori_order == "Jasa") {
                $jasa[$i] = $values_pdf_page2[$i];
                $jasa_volume[$i] = $jasa[$i]->volume;
                $ubah_volume_jasa[$i] = str_replace(".", ",", "$jasa_volume[$i]");
                $jasa[$i]->volume = $ubah_volume_jasa[$i];
                // $jasa[$i]->volume = str_replace()
            } else {
                $material[$i] = $values_pdf_page2[$i];
                $material_volume[$i] = $material[$i]->volume;
                $ubah_volume_material[$i] = str_replace(".", ",", "$material_volume[$i]");
                $material[$i]->volume = $ubah_volume_material[$i];
            }
        }
        // dd($jasa);
        // $jabatan = Pejabat::select('jabatan');

        $jabatan_manager = Pejabat::where('jabatan', 'Manager UP3')->value('jabatan');
        $nama_manager = Pejabat::where('jabatan', 'Manager UP3')->value('nama_pejabat');

        $jumlah = OrderKhs::where('rab_id', $rab_id)->sum('jumlah_harga');
        $ppn = $jumlah * 0.11;


        $pdf = Pdf::loadView('layouts.surat',[
            "po_khs" => $values_pdf_page1,
            "kategori_jasa" => $jasa,
            "kategori_material" => $material,
            "jumlah" => $jumlah,
            "ppn" => $ppn,
            "days" => $days,
            "jabatan_manager" => $jabatan_manager,
            "nama_manager" => $nama_manager,
            "title" => $nama_pdf,
            "redaksis" => $redaksis,
            "lokasis" => $lokasis,
        ]);

        $path1 = 'SPBJ.pdf';
        Storage::disk('local')->put($path1, $pdf->output());

        $pdf2 = Pdf::loadView('layouts.surat_jtm',[
            "po_khs" => $values_pdf_page1,
            "kategori_jasa" => $jasa,
            "kategori_material" => $material,
            "jumlah" => $jumlah,
            "ppn" => $ppn,
            "days" => $days,
            "jabatan_manager" => $jabatan_manager,
            "nama_manager" => $nama_manager,
            "title" => $nama_pdf,
            "redaksis" => $redaksis,
            "lokasis" => $lokasis,
        ]);

        // $content = $pdf->download()->getOriginalContent();
        // Storage::put('public/storage/file-pdf-khs/'.$nama_pdf.'.pdf',$content);
        $pdf2->setPaper('A4', 'landscape');
        $path2 = 'RAB.pdf';
        Storage::disk('local')->put($path2, $pdf2->output());

        $oMerger = PDFMerger::init();
        $oMerger->addPDF(Storage::disk('local')->path($path1), 'all');
        $oMerger->addPDF(Storage::disk('local')->path($path2), 'all');

        $oMerger->merge();
        $oMerger->save('storage/storage/file-pdf-khs/tkdn/'.$nama_pdf.'.pdf');

        $updated_prk_terkontrak = 0;
        $previous_prk_terkontrak = Rab::where('prk_id', $request->prk_id)->get('total_harga');
        foreach ($previous_prk_terkontrak as $prk_terkontrak)
            $updated_prk_terkontrak += (float)$prk_terkontrak->total_harga;
        Prk::where('id', $request->prk_id)->update(array('prk_terkontrak' => (float)$updated_prk_terkontrak));

        //Update PRK Sisa
        $pagu_prk = Prk::where('id', $request->prk_id)->value('pagu_prk');
        $prk_terkontrak = Prk::where('id', $request->prk_id)->value('prk_terkontrak');
        $updated_prk_sisa = (float)$pagu_prk - (float)$prk_terkontrak;
        Prk::where('id', $request->prk_id)->update(array('prk_sisa' => (float)$updated_prk_sisa));

        //Update SKK Terkontrak
        $updated_skk_terkontrak = 0;
        $previous_skk_terkontrak = Prk::where('no_skk_prk', $request->skk_id)->get('prk_terkontrak');
        foreach ($previous_skk_terkontrak as $skk_terkontrak)
            $updated_skk_terkontrak += (float)$skk_terkontrak->prk_terkontrak;
        Skk::where('id', $request->skk_id)->update(array('skk_terkontrak' => (float)$updated_skk_terkontrak));

        //Update SKK Sisa
        $pagu_skk = Skk::where('id', $request->skk_id)->value('pagu_skk');
        $skk_terkontrak = Skk::where('id', $request->skk_id)->value('skk_terkontrak');
        $updated_skk_sisa = (float)$pagu_skk - (float)$skk_terkontrak;
        Skk::where('id', $request->skk_id)->update(array('skk_sisa' => (float)$updated_skk_sisa));
        // $id = compact('id');
        return response()->json($id);
    }



    //CETAK TKDN PAKET LAMPIRAN


    public function cetak_paket_tkdn_lampiran(Request $request)
    {

        dd($request);
        // $request->validate([
        //     'nomor_po' => 'required|unique:rabs|max:250',
        //     'tanggal_po' => 'required|max:250',
        //     'skk_id' => 'required|max:250',
        //     'prk_id' => 'required|max:250',
        //     'pekerjaan' => 'required|max:250',
        //     'lokasi' => 'nullable|max:250',
        //     'startdate' => 'required|max:250',
        //     'enddate' => 'required|max:250',
        //     'nomor_kontrak_induk' => 'required|max:250',
        //     'addendum_id' => 'nullable|max:250',
        //     'pejabat_id' => 'required|max:250',
        //     'pengawas' => 'required|max:250',
        //     'total_harga' => 'required|max:250',
        //     'kategori_order' => 'required|max:250',
        //     'item_order' => 'required|max:250',
        //     'satuan_id' => 'required|max:250',
        //     'harga_satuan' => 'required|max:250',
        //     'volume' => 'required|max:250',
        //     'jumlah_harga' => 'required|max:250',
        //     // 'jumlah_harga' => 'required',
        // ]);
        // dd($request->group_location_step2);



        $addendum_id = Addendum::where('nomor_addendum', $request->addendum_id)->value('id');

        $nama_pdf = $request->nomor_po;
        $nama_pdf = str_replace('.', '_', $nama_pdf);
        $nama_pdf = str_replace('/','-', $nama_pdf);
        $nama_pdf = str_replace(' ','-', $nama_pdf);

        // dd($nama_pdf);

        $mypdf = 'storage/storage/file-pdf-khs/tkdn/'.$nama_pdf.'.pdf';

        $rab = [
            'nomor_po' => $request->nomor_po,
            'tanggal_po' => $request->tanggal_po,
            'skk_id' => $request->skk_id,
            'prk_id' => $request->prk_id,
            'pekerjaan' => $request->pekerjaan,
            // 'lokasi' => $request->lokasi,
            'startdate' => $request->startdate,
            'enddate' => $request->enddate,
            'nomor_kontrak_induk' => $request->nomor_kontrak_induk,
            'addendum_id' => $addendum_id,
            'pejabat_id' => $request->pejabat_id,
            'pengawas_pekerjaan' => $request->pengawas_pekerjaan,
            'total_harga' => $request->total_harga,
            'pdf_file' =>$mypdf,
        ];

        Rab::create($rab);
        // dd($rab);

        $id = Rab::where('nomor_po', $request->nomor_po)->value('id');

        $total_tabel = $request->click;

        $rab_id = [];
        $satuan_id = [];
        $nama_item_id = [];

        for($i = 0; $i < count($request->lokasi_with_paket); $i++){
            $rab_id[$i] = $id;
            $order_lokasi = [
                'rab_id' => $rab_id[$i],
                'nama_lokasi' => $request->lokasi_with_paket[$i]
            ];
            lokasi::create($order_lokasi);
            $lokasi_id = lokasi::where('rab_id', $rab_id[$i])->where("nama_lokasi", $request->lokasi_with_paket[$i])->value('id');
            for($j = 0; $j < count($request->pakets[$i]); $j++){
                // $rab_id[$i] = $id;
                $order_paket = [
                    'nama_paket' => $request->pakets[$i][$j],
                    'lokasi_id' => $lokasi_id,
                ];
                OrderPaket::create($order_paket);
                $order_paket_id = OrderPaket::where("lokasi_id", $lokasi_id)->where("nama_paket", $request->pakets[$i][$j])->value('id');
                // dd($request->item_id[$i]);
                for($k = 0; $k < count($request->item_id[$i][$j]); $k++){
                    $satuan_id = Satuan::where('kepanjangan', $request->satuan_id_with_paket[$i][$j][$k])->value('id');
                    $item_order = RincianInduk::where('nama_item', $request->item_id[$i][$j][$k])->value('id');
                    $order_khs = [
                        'rab_id' => $rab_id[$i],
                        'order_paket_id' => $order_paket_id,
                        'kategori_order' => $request->kategory_order_with_paket[$i][$j][$k],
                        'item_order' => $item_order,
                        'satuan_id' => $satuan_id,
                        'harga_satuan' => $request->harga_satuan_with_paket[$i][$j][$k],
                        'volume' => $request->volume_with_paket[$i][$j][$k],
                        'jumlah_harga' => $request->jumlah_harga_with_paket[$i][$j][$k],
                        'tkdn' => $request->tkdn_with_paket[$i][$j][$k],
                        'kdn' => $request->tkdn_with_paket[$i][$j][$k] * $request->jumlah_harga_with_paket[$i][$j][$k],
                        'kln' =>  $request->jumlah_harga_with_paket[$i][$j][$k] - ($request->tkdn_with_paket[$i][$j][$k] * $request->jumlah_harga_with_paket[$i][$j][$k]),
                        'total_tkdn' => ($request->tkdn_with_paket[$i][$j][$k] * $request->jumlah_harga_with_paket[$i][$j][$k]) + ($request->jumlah_harga_with_paket[$i][$j][$k] - ($request->tkdn_with_paket[$i][$j][$k] * $request->jumlah_harga_with_paket[$i][$j][$k]))
                    ];
                    OrderKhs::create($order_khs);
                }
                // $nama_item_id = RincianINduk::where('nama_item', $request->item_order[$i])->value('id');
            }
        }
        // dd($order_khs);
        // dd
        for ($i = 0; $i < count($request->sub_deskripsi_id); $i++) {
            // $subdeksripsi_id[$i] = SubRedaksi::where('sub_deskripsi', $request->sub_deskripsi_id[$i])->where('redaksi_id', $request->redaksi_id)->value('id'),
            $sub_deskripsi_id = SubRedaksi::where('sub_deskripsi', $request->sub_deskripsi_id[$i])->where('redaksi_id', $request->redaksi_id)->value('id');
            $rab_redaksi = [
                "rab_id" => $id,
                "subdeskripsi_id" => $sub_deskripsi_id
            ];
            RabRedaksi::create($rab_redaksi);
        }

        dd($rab_redaksi);


        $redaksi_click = $request->clickredaksi;
        for ($i = 0; $i < $redaksi_click; $i++) {
            $rab_id[$i] = $id;
        }
        for ($j = 0; $j < $redaksi_click; $j++) {
            $order_redaksi = [
                'rab_id' => $rab_id[$j],
                'redaksi_id' => $request->redaksi_id[$j],
                'deskripsi_id' => $request->deskripsi_id[$j],
                'sub_deskripsi_id' => $request->sub_deskripsi_id[$j],
            ];
            OrderRedaksiKHS::create($order_redaksi);
        }

        $lokasi_click = $request->clicklokasi;
        for ($i = 0; $i < $lokasi_click; $i++) {
            $rab_id[$i] = $id;
        }



        $redaksis = OrderRedaksiKHS::where('rab_id', $rab_id)->get();
        $lokasis = lokasi::where('rab_id', $rab_id)->get();
        // dd($redaksis);

        $values_pdf_page1 = Rab::where('id', $id)->get();

        $startdate = Rab::where('id', $id)->value('startdate');
        $enddate = Rab::where('id', $id)->value('enddate');
        $datetime1 = new DateTime($startdate);
        $datetime2 = new DateTime($enddate);
        $interval = new DatePeriod($datetime1, new DateInterval('P1D'), $datetime2);
        $d = 0;
        $days = 0;
        $datetime2 = 1;

        foreach($interval as $date) {
            $interval = $date->format("Y-m-d");
            $datetime = DateTime::createFromFormat('Y-m-d', $interval);

            $day = $datetime->format('D');

            if($day != "Sun" && $day != "Sat") {
                $days += $datetime2 - $d;
            }

            $datetime2++;
            $d++;
        }

        $rab_id = Rab::where('id', $id)->value('id');
        $values_pdf_page2 = OrderKhs::where('rab_id', $rab_id)->get();

        $jasa = [];
        $jasa_volume = [];
        $ubah_volume_jasa = [];
        $material = [];
        $material_volume = [];
        $ubah_volume_material = [];

        for($i = 0; $i < count($values_pdf_page2); $i++) {
            if($values_pdf_page2[$i]->kategori_order == "Jasa") {
                $jasa[$i] = $values_pdf_page2[$i];
                $jasa_volume[$i] = $jasa[$i]->volume;
                $ubah_volume_jasa[$i] = str_replace(".", ",", "$jasa_volume[$i]");
                $jasa[$i]->volume = $ubah_volume_jasa[$i];
                // $jasa[$i]->volume = str_replace()
            } else {
                $material[$i] = $values_pdf_page2[$i];
                $material_volume[$i] = $material[$i]->volume;
                $ubah_volume_material[$i] = str_replace(".", ",", "$material_volume[$i]");
                $material[$i]->volume = $ubah_volume_material[$i];
            }
        }
        // dd($jasa);
        // $jabatan = Pejabat::select('jabatan');

        $jabatan_manager = Pejabat::where('jabatan', 'Manager UP3')->value('jabatan');
        $nama_manager = Pejabat::where('jabatan', 'Manager UP3')->value('nama_pejabat');

        $jumlah = OrderKhs::where('rab_id', $rab_id)->sum('jumlah_harga');
        $ppn = $jumlah * 0.11;


        $pdf = Pdf::loadView('format_surat.redaksi_jtm',[
            "po_khs" => $values_pdf_page1,
            "kategori_jasa" => $jasa,
            "kategori_material" => $material,
            "jumlah" => $jumlah,
            "ppn" => $ppn,
            "days" => $days,
            "jabatan_manager" => $jabatan_manager,
            "nama_manager" => $nama_manager,
            "title" => $nama_pdf,
            "redaksis" => $redaksis,
            "lokasis" => $lokasis,
        ]);

        $path1 = 'SPBJ.pdf';
        Storage::disk('local')->put($path1, $pdf->output());

        $pdf2 = Pdf::loadView('format_surat.rab_tkdn',[
            "po_khs" => $values_pdf_page1,
            "kategori_jasa" => $jasa,
            "kategori_material" => $material,
            "jumlah" => $jumlah,
            "ppn" => $ppn,
            "days" => $days,
            "jabatan_manager" => $jabatan_manager,
            "nama_manager" => $nama_manager,
            "title" => $nama_pdf,
            "redaksis" => $redaksis,
            "lokasis" => $lokasis,
        ]);

        // $content = $pdf->download()->getOriginalContent();
        // Storage::put('public/storage/file-pdf-khs/'.$nama_pdf.'.pdf',$content);
        $pdf2->setPaper('A4', 'landscape');
        $path2 = 'RAB.pdf';
        Storage::disk('local')->put($path2, $pdf2->output());

        $oMerger = PDFMerger::init();
        $oMerger->addPDF(Storage::disk('local')->path($path1), 'all');
        $oMerger->addPDF(Storage::disk('local')->path($path2), 'all');
        $oMerger->addPDF($request->file('lampiran')->getPathName(), 'all');

        $oMerger->merge();
        $oMerger->save('storage/storage/file-pdf-khs/tkdn/'.$nama_pdf.'.pdf');

        $updated_prk_terkontrak = 0;
        $previous_prk_terkontrak = Rab::where('prk_id', $request->prk_id)->get('total_harga');
        foreach ($previous_prk_terkontrak as $prk_terkontrak)
            $updated_prk_terkontrak += (float)$prk_terkontrak->total_harga;
        Prk::where('id', $request->prk_id)->update(array('prk_terkontrak' => (float)$updated_prk_terkontrak));

        //Update PRK Sisa
        $pagu_prk = Prk::where('id', $request->prk_id)->value('pagu_prk');
        $prk_terkontrak = Prk::where('id', $request->prk_id)->value('prk_terkontrak');
        $updated_prk_sisa = (float)$pagu_prk - (float)$prk_terkontrak;
        Prk::where('id', $request->prk_id)->update(array('prk_sisa' => (float)$updated_prk_sisa));

        //Update SKK Terkontrak
        $updated_skk_terkontrak = 0;
        $previous_skk_terkontrak = Prk::where('no_skk_prk', $request->skk_id)->get('prk_terkontrak');
        foreach ($previous_skk_terkontrak as $skk_terkontrak)
            $updated_skk_terkontrak += (float)$skk_terkontrak->prk_terkontrak;
        Skk::where('id', $request->skk_id)->update(array('skk_terkontrak' => (float)$updated_skk_terkontrak));

        //Update SKK Sisa
        $pagu_skk = Skk::where('id', $request->skk_id)->value('pagu_skk');
        $skk_terkontrak = Skk::where('id', $request->skk_id)->value('skk_terkontrak');
        $updated_skk_sisa = (float)$pagu_skk - (float)$skk_terkontrak;
        Skk::where('id', $request->skk_id)->update(array('skk_sisa' => (float)$updated_skk_sisa));
        // $id = compact('id');
        return response()->json($id);
    }

    public function download($id)
    {
         $values_pdf_page1 = Rab::where('id', $id)->get();

        $startdate = Rab::where('id', $id)->value('startdate');
        $enddate = Rab::where('id', $id)->value('enddate');
        $datetime1 = new DateTime($startdate);
        $datetime2 = new DateTime($enddate);
        $interval = new DatePeriod($datetime1, new DateInterval('P1D'), $datetime2);
        $d = 0;
        $days = 0;
        $datetime2 = 1;

        foreach($interval as $date) {
            $interval = $date->format("Y-m-d");
            $datetime = DateTime::createFromFormat('Y-m-d', $interval);

            $day = $datetime->format('D');

            if($day != "Sun" && $day != "Sat") {
                $days += $datetime2 - $d;
            }

            $datetime2++;
            $d++;
        }
        // $days = $interval->format('%a');

        $rab_id = Rab::where('id', $id)->value('id');
        $values_pdf_page2 = OrderKhs::where('rab_id', $rab_id)->get();
        $redaksis = OrderRedaksiKHS::where('rab_id', $rab_id)->get();
        $lokasis = lokasi::where('rab_id', $rab_id)->get();

        // dd($values_pdf_page2[0]->kategori_order);
        $jasa = [];
        $material = [];

        for($i = 0; $i < count($values_pdf_page2); $i++) {
            if($values_pdf_page2[$i]->kategori_order == "Jasa") {
                $jasa[$i] = $values_pdf_page2[$i];
            } else {
                $material[$i] = $values_pdf_page2[$i];
            }
        }

        $jabatan_manager = Pejabat::where('jabatan', 'Manager UP3')->value('jabatan');
        $nama_manager = Pejabat::where('jabatan', 'Manager UP3')->value('nama_pejabat');

        $jumlah = OrderKhs::where('rab_id', $rab_id)->sum('jumlah_harga');
        $ppn = $jumlah * 0.11;


        $pdf = Pdf::loadView('format_surat.redaksi_jtm',[
            "po_khs" => $values_pdf_page1,
            "kategori_jasa" => $jasa,
            "kategori_material" => $material,
            "jumlah" => $jumlah,
            "ppn" => $ppn,
            "days" => $days,
            "jabatan_manager" => $jabatan_manager,
            "nama_manager" => $nama_manager,
            "redaksis" => $redaksis,
            "lokasis" => $lokasis,
            "title" => 'PO-KHS (SP-APP)',
        ]);
        $path1 = 'SPBJ.pdf';
        Storage::disk('local')->put($path1, $pdf->output());


        $pdf2 = Pdf::loadView('format_surat.rab_tkdn',[
            "po_khs" => $values_pdf_page1,
            "kategori_jasa" => $jasa,
            "kategori_material" => $material,
            "jumlah" => $jumlah,
            "ppn" => $ppn,
            "days" => $days,
            "jabatan_manager" => $jabatan_manager,
            "nama_manager" => $nama_manager,
            "redaksis" => $redaksis,
            "lokasis" => $lokasis,
            "title" => 'PO-KHS (SP-APP)',
        ]);
        $pdf2->setPaper('A4', 'landscape');
        $path2 = 'RAB.pdf';
        Storage::disk('local')->put($path2, $pdf2->output());

        $oMerger = PDFMerger::init();
        $oMerger->addPDF(Storage::disk('local')->path($path1), 'all');
        $oMerger->addPDF(Storage::disk('local')->path($path2), 'all');

        $oMerger->merge();

        return $oMerger->download();

    }





}
