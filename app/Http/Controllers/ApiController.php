<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Alat;
use App\Models\AlatRuang;
use App\Models\Eselon1;
use App\Models\Eselon2;
use App\Models\Notifikasi;
use App\Models\Pinjam;
use App\Models\PinjamAlat;
use App\Models\PinjamNotes;
use App\Models\PinjamRate;
use App\Models\Role;
use App\Models\Ruang;
use App\Models\Slider;
use App\User;
use Hash;
use Storage;
class ApiController extends Controller
{
    public function alat()
    {
        $alat=Alat::select('id','kapasitas','nama')->orderBy('nama')->get();
        if($alat->count()!=0)
        {
            $data['data']=$alat;
            $data['status']='success';
        }
        else
        {
            $data['data']=array();
            $data['status']='error';
        }
        return $data;
    }

    public function alat_ruang()
    {
        $alat=AlatRuang::join('alat', 'alat.id', '=', 'alat_ruang.alat_id')
                ->join('ruang', 'ruang.id', '=', 'alat_ruang.ruang_id')
                ->select('alat_ruang.id as alatruang_id','alat.id as alat_id','alat.kapasitas as kapasitas_alat','alat.nama as nama_alat','ruang.*')
                ->get();

        
        if($alat->count()!=0)
        {
            $data['data']=$alat;
            $data['status']='success';
        }
        else
        {
            $data['data']=array();
            $data['status']='error';
        }
        return $data;
    }
    public function eselon_1()
    {
         $eselon=Eselon1::select('id','kode','nama')->orderBy('nama')->get();
                
        if($eselon->count()!=0)
        {
            $data['data']=$eselon;
            $data['status']='success';
        }
        else
        {
            $data['data']=array();
            $data['status']='error';
        }
        return $data;
    }
    public function eselon_2()
    {
        $eselon=Eselon1::join('eselon2','eselon2.eselon1_id','=','eselon1.id')
                ->select('eselon1.id as es1_id','eselon1.kode as es1_kode','eselon1.nama as es1_nama','eselon2.*')
                ->orderBy('eselon2.kode')->get();
                
        if($eselon->count()!=0)
        {
            $data['data']=$eselon;
            $data['status']='success';
        }
        else
        {
            $data['data']=array();
            $data['status']='error';
        }
        return $data;
    }
    public function notifikasi()
    {
        $notif=Notifikasi::join('users','users.id','=','notifikasi.user_id')
                ->join('pinjam','pinjam.id','=','notifikasi.pinjam_id')
                ->select('notifikasi.id as notif_id','notifikasi.category as notif_category','notifikasi.message as notif_msg','pinjam.*')
                ->orderBy('notifikasi.created_at')->get();
                
        if($notif->count()!=0)
        {
            $data['data']=$notif;
            $data['status']='success';
        }
        else
        {
            $data['data']=array();
            $data['status']='error';
        }
        return $data;
    }
    public function pinjam()
    {
        $pinjam=Pinjam::with('peminjam')->with('ruang')->with('pinjamnotes')->with('user')->with('pinjamalat')->orderBy('mulai','desc')->orderBy('selesai','desc')->get();
        if($pinjam->count()!=0)
        {
            $data['data']=$pinjam;
            $data['status']='success';
        }
        else
        {
            $data['data']=array();
            $data['status']='error';
        }
        return $data;
    }
    public function pinjam_by_peminjam($id)
    {
        $pinjam=Pinjam::where('users_peminjam_id',$id)->with('peminjam')->with('ruang')->with('pinjamnotes')->with('user')->with('pinjamalat')->orderBy('mulai','desc')->orderBy('selesai','desc')->get();
        if($pinjam->count()!=0)
        {
            $pinj=array();
            $x=0;
            foreach($pinjam as $k=>$v)
            {
                
                $pinj[$x]=$v;
                $pinj[$x]['mulai']=date('d-m-Y H:i:s',strtotime($v->mulai));
                $pinj[$x]['selesai']=date('d-m-Y H:i:s',strtotime($v->selesai));
                // $pinj[$x]['rate']=floatval($v->rate);
                $pinjamAlat=PinjamAlat::where('pinjam_id',$v->id)->with('alat')->get();
                $xx=0;
                foreach($pinjamAlat as $ka=>$va)
                {
                    $pinj[$x]['pinjamalat'][$xx]['id']=$va->id;
                    $pinj[$x]['pinjamalat'][$xx]['created_at']=$va->created_at;
                    $pinj[$x]['pinjamalat'][$xx]['updated_at']=$va->updated_at;
                    $pinj[$x]['pinjamalat'][$xx]['jumlah']=$va->jumlah;
                    $pinj[$x]['pinjamalat'][$xx]['alat_id']=$va->alat_id;
                    $pinj[$x]['pinjamalat'][$xx]['pinjam_id']=$va->pinjam_id;
                    $pinj[$x]['pinjamalat'][$xx]['keterangan']=$va->keterangan;
                    $pinj[$x]['pinjamalat'][$xx]['nama']=$va->alat->nama;
                    $pinj[$x]['pinjamalat'][$xx]['kapasitas']=$va->alat->kapasitas;
                    $xx++;
                }
                $x++;
            }
            $data['data']=$pinj;
            // $data['data']=$pinjam;
            $data['status']='success';
        }
        else
        {
            $data['data']=array();
            $data['status']='error';
        }
        return $data;
    }
    public function pinjam_by_ruang($id)
    {
        $pinjam=Pinjam::where('ruang_id',$id)->with('peminjam')->with('ruang')->with('pinjamnotes')->with('user')->with('pinjamalat')->orderBy('mulai','desc')->orderBy('selesai','desc')->get();
        if($pinjam->count()!=0)
        {
            $pinj=array();
            $x=0;
            foreach($pinjam as $k=>$v)
            {
                $pinj[$x]=$v;
                $pinj[$x]['mulai']=date('d-m-Y H:i:s',strtotime($v->mulai));
                $pinj[$x]['selesai']=date('d-m-Y H:i:s',strtotime($v->selesai));
                $x++;
            }
            $data['data']=$pinj;
            // $data['data']=$pinjam;
            $data['status']='success';
        }
        else
        {
            $data['data']=array();
            $data['status']='error';
        }
        return $data;
    }
   
    public function pinjam_by_month($month,$year)
    {
        // $pinjam=Pinjam::where('ruang_id',$id)->with('peminjam')->with('ruang')->with('pinjamnotes')->with('user')->with('pinjamalat')->orderBy('mulai','desc')->orderBy('selesai','desc')->get();
        // $pinjam=Pinjam::whereRaw('(month(mulai)='.$month.' OR month(selesai)='.$month.'')
        // $pinjam=Pinjam::whereRaw('EXTRACT(MONTH FROM mulai)','=',$month)
        $pinjam=Pinjam::whereRaw('extract(month from mulai) = ?', [$month])
            ->orWhereRaw('extract(month from selesai) = ?', [$month])
            ->with('peminjam')->with('ruang')->with('pinjamnotes')->with('peminjam')->with('user')->with('pinjamalat')
            ->orderBy('mulai','desc')
            ->orderBy('selesai','desc')->get();
        // return $pinjam;
        if($pinjam->count()!=0)
        {
            $pinj=array();
            foreach($pinjam as $k=>$v)
            {
                list($thn,$bln,$tgl)=explode('-',strtok($v->mulai,' '));
                if((int)$bln==$month && $year==$thn)
                {
                    $tgl=strtok($v->mulai,' ');
                    $tgl2=strtok($v->selesai,' ');
                    $period=$this->date_range($tgl, $tgl2, "+1 day", "Y-m-d");
                    foreach($period as $pk=>$pv)
                    {
                        // $pinj[$tgl][]=$v;
                        $pinj[$pv][]=$v;
                    }
                    // $pinj[]=$v;
                }
                
                list($thn,$bln,$tgl)=explode('-',strtok($v->selesai,' '));
                if((int)$bln==$month && $year==$thn)
                {
                    $tgl=strtok($v->mulai,' ');
                    $tgl2=strtok($v->selesai,' ');
                    $period=$this->date_range($tgl, $tgl2, "+1 day", "Y-m-d");
                    foreach($period as $pk=>$pv)
                    {
                        // $pinj[$tgl][]=$v;
                        $pinj[$pv][]=$v;
                    }
                    // $pinj[]=$v;
                }
            }
            $x=0;
            $pjm=array();
            foreach($pinj as $k=>$v)
            {
                $pjm[$x]['date']=$k;
                $idx=0;
                foreach($v as $kk=>$item)
                {
                    $sl=explode(' ',$item->selesai);
                    $mul=explode(' ',$item->mulai);
                    $pjm[$x]['event'][$idx]['id']=$item->id;
                    $pjm[$x]['event'][$idx]['created_at']=date('Y-m-d H:i:s',strtotime($item->created_at));
                    $pjm[$x]['event'][$idx]['name']=$item->topik;
                    $pjm[$x]['event'][$idx]['waktu_mulai']=$mul[1];
                    $pjm[$x]['event'][$idx]['waktu_selesai']=$sl[1];
                    $pjm[$x]['event'][$idx]['tgl_selesai']=date('d-m-Y',strtotime(trim(strtok($item->selesai,' '))));
                    $pjm[$x]['event'][$idx]['ruang']=$item->ruang->nama;
                    $pjm[$x]['event'][$idx]['agenda']=$item->topik;
                    $pjm[$x]['event'][$idx]['jumlah_peserta']=$item->jumlah_peserta;
                    $pjm[$x]['event'][$idx]['pimpinan_rapat']=$item->pimpinan_rapat;
                    $pjm[$x]['event'][$idx]['keterangan']=$item->keterangan;
                    $pjm[$x]['event'][$idx]['lampiran']=$item->undangan;
                    $pjm[$x]['event'][$idx]['satker']=isset($item->peminjam->eselon2->nama) ? $item->peminjam->eselon2->nama : '-';
                    
                    if($item->layout==1)
                        $pjm[$x]['event'][$idx]['tata_letak']='Class Room';
                    elseif($item->layout==2)
                        $pjm[$x]['event'][$idx]['tata_letak']='U Shape';
                    elseif($item->layout==3)
                        $pjm[$x]['event'][$idx]['tata_letak']='Theater';
                    elseif($item->layout==4)
                        $pjm[$x]['event'][$idx]['tata_letak']='Upacara';
                    elseif($item->layout==5)
                        $pjm[$x]['event'][$idx]['tata_letak']='Lainnya';
                    else
                        $pjm[$x]['event'][$idx]['tata_letak']='-';
                    // $pjm[$x]['event'][$idx]['notes']=isset($item->pinjamnotes->notes) ? $item->pinjamnotes->notes : '-';

                    $pinjamnote=PinjamNotes::where('pinjam_id',$item->id)->with('user')->get();
                    $notes=$lampr='';
                    $pjm[$x]['event'][$idx]['attachment']['name']='';
                    $pjm[$x]['event'][$idx]['attachment']['path']='';
                    foreach($pinjamnote as $k=>$v)
                    {
                        if($v->notes!='')
                            $notes.=$v->notes.'<br>';

                        if($v->lampiran!='' && $v->lampiran!=NULL)
                        {
                            $lampr=explode('/',$v->lampiran);
                            $pjm[$x]['event'][$idx]['attachment']['name']=$lampr[count($lampr)-1];
                            $pjm[$x]['event'][$idx]['attachment']['path']=$v->lampiran;
                        }
                        if($v->pengguna_pic_pinjam_id!='' && $v->pengguna_pic_pinjam_id!=NULL)
                        {
                            $pjm[$x]['event'][$idx]['picname']=$v->user->name;
                        }
                    }
                    
                    
                    $pjm[$x]['event'][$idx]['notes']=$notes; 
                    $pinjamalat=PinjamAlat::where('pinjam_id',$item->id)->get();
                    if($pinjamalat->count()!=0)
                    {

                        foreach($pinjamalat as $k=>$v)
                        {
                           $pjm[$x]['event'][$idx]['pinjam_alat'][]=$v->alat->nama; 
                        }
                    }
                    else
                        $pjm[$x]['event'][$idx]['pinjam_alat']=array();
                    // $pjm[$x]['event'][$idx]['pinjam_alat']=isset($item->pinjamalat->id) ? $item->pinjamalat->id : '-';
                    // $pjm[$x]['event'][$idx]['satker']=$item->peminjam->id;
                    $idx++;
                }
                $x++;
            }
            $data['data']=$pjm;
            $data['status']='success';
        }
        else
        {
            $data['data']=array();
            $data['status']='error';
        }
        return $data;
    }
    public function getbydate($date,$time,$idruang)
    {
        $datetime=strtotime($date.' '.$time);
        $pinjam=Pinjam::whereDate('mulai', $date)->where('ruang_id',$idruang)->with('peminjam')->with('ruang')->with('pinjamnotes')->with('user')->with('pinjamalat')->orderBy('mulai','desc')->orderBy('selesai','desc')->get();
        if($pinjam->count()!=0)
        {
            $pinj=array();
            $x=0;
            foreach($pinjam as $k=>$v)
            {
                // if(strpos($v->mulai,$datetime)!==false)
                // {
                // $tgl=strtok($v->mulai,' ');
                // $tgl2=strtok($v->selesai,' ');
                    $tgl=$v->mulai;
                    $tgl2=$v->selesai;
                    // $pinj[]=$v;
                // }
                $start = strtotime($tgl);
                $end = strtotime($tgl2);
                // $time=
                if($datetime >= $start && $datetime <= $end) {
                // ok
                    $pinj[$x]['ruangan']=$v->ruang->nama;
                    $pinj[$x]['mulai']=$tgl;
                    $pinj[$x]['selesai']=$tgl2;
                    $pinj[$x]['event']=$v->topik;
                    $pinj[$x]['id']=$v->id;
                    $pinj[$x]['status']=$v->status;
                    $x++;
                } else {
                // not ok
                }
                // $period=$this->date_range($tgl, $tgl2, "+1 day", "Y-m-d H:i:s");
                // foreach($period as $pk=>$pv)
                // {
                //     // $pinj[$tgl][]=$v;
                //     $pinj[$pv]=$v;
                // }
            }
            // return $pinj;
            if(count($pinj)!=0)
            {
                $data['data']=$pinj;
                $data['statuspinjam']='ada';
                $data['status']='success';
            }
            else{

                $data['data']=array();
                $data['statuspinjam']='tidak';
                $data['status']='success';
            }
        }
        else
        {
            $data['data']=array();
            $data['statuspinjam']='tidak';
            $data['status']='error';
        }
        return $data;
    }

    public function jadwal_all_manager()
    {
        $pinjam=Pinjam::with('peminjam')->with('ruang')->with('pinjamnotes')->with('user')->with('pinjamalat')->orderBy('created_at','desc')->get();
        // $pinjam=Pinjam::with('peminjam')->with('ruang')->with('pinjamnotes')->with('user')->with('pinjamalat')->orderBy('mulai','desc')->get();
        if($pinjam->count()!=0)
        {
            $pinj=array();
            $x=0;
            foreach($pinjam as $k=>$v)
            {
                $pinj[$x]=$v;
                $pinj[$x]['mulai']=date('d-m-Y H:i:s',strtotime($v->mulai));
                $pinj[$x]['selesai']=date('d-m-Y H:i:s',strtotime($v->selesai));
                $x++;
            }
            $data['data']=$pinj;
            // $data['pinjam']=$pinjam;
            $data['status']='success';
            
        }
        else
        {
            $data['data']=array();
            $data['status']='error';
        }
        return $data;
    }
    public function jadwal_by_status($iduser,$status)
    {
        if($iduser==-1)
        {
            if($status==-1)
                $pinjam=Pinjam::with('peminjam')->with('ruang')->with('pinjamnotes')->with('user')->with('pinjamalat')->orderBy('mulai','desc')->orderBy('selesai','desc')->get();
            else
                $pinjam=Pinjam::where('status',$status)->with('peminjam')->with('ruang')->with('pinjamnotes')->with('user')->with('pinjamalat')->orderBy('mulai','desc')->orderBy('selesai','desc')->get();
        }
        else
        {
            if($status==-1)
                $pinjam=Pinjam::where('users_peminjam_id',$iduser)->with('peminjam')->with('ruang')->with('pinjamnotes')->with('user')->with('pinjamalat')->orderBy('mulai','desc')->orderBy('selesai','desc')->get();
            else
                $pinjam=Pinjam::where('users_peminjam_id',$iduser)->where('status',$status)->with('peminjam')->with('ruang')->with('pinjamnotes')->with('user')->with('pinjamalat')->orderBy('mulai','desc')->orderBy('selesai','desc')->get();
        }

        if($pinjam->count()!=0)
        {
            $pinj=array();
            $x=0;
            foreach($pinjam as $k=>$v)
            {
                $pinj[$x]=$v;
                $pinj[$x]['mulai']=date('d-m-Y H:i:s',strtotime($v->mulai));
                $pinj[$x]['selesai']=date('d-m-Y H:i:s',strtotime($v->selesai));
                $x++;
            }
            $data['data']=$pinj;
            // $data['pinjam']=$pinjam;
            $data['status']='success';
            
        }
        else
        {
            $data['data']=array();
            $data['status']='error';
        }
        return $data;
    }

    public function pinjam_by_date($date1,$date2)
    {
        // $pinjam=Pinjam::whereBetween('mulai', [$date1, $date2])->orWhereBetween('selesai', [$date1, $date2])
        //         ->with('peminjam')->with('ruang')->with('pinjamnotes')->with('user')->with('pinjamalat')->orderBy('mulai','desc')->orderBy('selesai','desc')->get();
        $pinjam=Pinjam::with('peminjam')->with('ruang')->with('pinjamnotes')->with('user')->with('pinjamalat')->orderBy('mulai','desc')->orderBy('selesai','desc')->get();
        // $pinjam=Pinjam::with('peminjam')->with('ruang')->with('pinjamnotes')->with('peminjam')->with('user')->with('pinjamalat')->orderBy('mulai','desc')->orderBy('selesai','desc')->get();
        // return $pinjam;
        if($pinjam->count()!=0)
        {
            // $pinj=array();
            $x=0;
          
            $pinj=$pinj2=array();
            $array_pinj=array();
            $period2=$this->date_range($date1, $date2, "+1 day", "Y-m-d");
            foreach($period2 as $kk=>$vv)
            {
                // if(in_array($vv,$array_pinj))
                    $pinj2[]=$vv;
            }
            foreach($pinjam as $k=>$v)
            {
                $tgl=strtok($v->mulai,' ');
                $tgl2=strtok($v->selesai,' ');
                $period=$this->date_range($tgl, $tgl2, "+1 day", "Y-m-d");
                
                foreach($period as $pk=>$pv)
                {
                    // $pinj[$pv][]=$v;
                    if(in_array($pv,$pinj2))
                        $pinj[$pv][]=$v;
                        // $array_pinj[]=$pv;
                    // echo $pv."\n";
                }
                // if(in_array($date1,$array_pinj))
                //     $pinj[$date1][]=$v;
                
                // if(in_array($date2,$array_pinj))
                //     $pinj[$date2][]=$v;

                
            }
            // rsort($pinj);
            // return $pinj;
            

            $x=0;
            $pjm=array();
            foreach($pinj as $k=>$v)
            {
                $pjm[$x]['date']=$k;
                $idx=0;
                foreach($v as $kk=>$item)
                {
                    $sl=explode(' ',$item->selesai);
                    $mul=explode(' ',$item->mulai);
                    $pjm[$x]['event'][$idx]['id']=$item->id;
                    $pjm[$x]['event'][$idx]['created_at']=date('Y-m-d H:i:s',strtotime($item->created_at));
                    $pjm[$x]['event'][$idx]['name']=$item->topik;
                    $pjm[$x]['event'][$idx]['waktu_mulai']=$mul[1];
                    $pjm[$x]['event'][$idx]['waktu_selesai']=$sl[1];
                    $pjm[$x]['event'][$idx]['tgl_selesai']=date('d-m-Y',strtotime(trim(strtok($item->selesai,' '))));
                    $pjm[$x]['event'][$idx]['ruang']=$item->ruang->nama;
                    $pjm[$x]['event'][$idx]['agenda']=$item->topik;
                    $pjm[$x]['event'][$idx]['jumlah_peserta']=$item->jumlah_peserta;
                    $pjm[$x]['event'][$idx]['pimpinan_rapat']=$item->pimpinan_rapat;
                    $pjm[$x]['event'][$idx]['keterangan']=$item->keterangan;
                    $pjm[$x]['event'][$idx]['lampiran']=$item->undangan;
                    $pjm[$x]['event'][$idx]['satker']=isset($item->peminjam->eselon2->nama) ? $item->peminjam->eselon2->nama : '-';
                    
                    if($item->layout==1)
                        $pjm[$x]['event'][$idx]['tata_letak']='Class Room';
                    elseif($item->layout==2)
                        $pjm[$x]['event'][$idx]['tata_letak']='U Shape';
                    elseif($item->layout==3)
                        $pjm[$x]['event'][$idx]['tata_letak']='Theater';
                    elseif($item->layout==4)
                        $pjm[$x]['event'][$idx]['tata_letak']='Upacara';
                    elseif($item->layout==5)
                        $pjm[$x]['event'][$idx]['tata_letak']='Lainnya';
                    else
                        $pjm[$x]['event'][$idx]['tata_letak']='-';
                    // $pjm[$x]['event'][$idx]['notes']=isset($item->pinjamnotes->notes) ? $item->pinjamnotes->notes : '-';

                    $pinjamnote=PinjamNotes::where('pinjam_id',$item->id)->with('user')->get();
                    $notes=$lampr='';
                    $pjm[$x]['event'][$idx]['attachment']['name']='';
                    $pjm[$x]['event'][$idx]['attachment']['path']='';
                    foreach($pinjamnote as $k=>$v)
                    {
                        if($v->notes!='')
                            $notes.=$v->notes.'<br>';

                        if($v->lampiran!='' && $v->lampiran!=NULL)
                        {
                            $lampr=explode('/',$v->lampiran);
                            $pjm[$x]['event'][$idx]['attachment']['name']=$lampr[count($lampr)-1];
                            $pjm[$x]['event'][$idx]['attachment']['path']=$v->lampiran;
                        }
                        if($v->pengguna_pic_pinjam_id!='' && $v->pengguna_pic_pinjam_id!=NULL)
                        {
                            $pjm[$x]['event'][$idx]['picname']=$v->user->name;
                        }
                    }
                    
                    
                    $pjm[$x]['event'][$idx]['notes']=$notes; 
                    $pinjamalat=PinjamAlat::where('pinjam_id',$item->id)->get();
                    if($pinjamalat->count()!=0)
                    {

                        foreach($pinjamalat as $k=>$v)
                        {
                           $pjm[$x]['event'][$idx]['pinjam_alat'][]=$v->alat->nama; 
                        }
                    }
                    else
                        $pjm[$x]['event'][$idx]['pinjam_alat']=array();
                    // $pjm[$x]['event'][$idx]['pinjam_alat']=isset($item->pinjamalat->id) ? $item->pinjamalat->id : '-';
                    // $pjm[$x]['event'][$idx]['satker']=$item->peminjam->id;
                    $idx++;
                }
                $x++;
            }
            $data['data']=$pjm;
            $data['status']='success';
        }
        else
        {
            $data['data']=array();
            $data['status']='error';
        }
        return $data;
    }

    public function pinjam_alat()
    {
        $pinjam=PinjamAlat::with('pinjam')->with('alat')->get();
        if($pinjam->count()!=0)
        {
            $data['data']=$pinjam;
            $data['status']='success';
        }
        else
        {
            $data['data']=array();
            $data['status']='error';
        }
        return $data;
    }
    public function pinjam_alat_by_pinjamid($id)
    {
        $pinjam=PinjamAlat::where('pinjam_id',$id)->with('pinjam')->with('alat')->get();
        if($pinjam->count()!=0)
        {
            $data['data']=$pinjam;
            $data['status']='success';
        }
        else
        {
            $data['data']=array();
            $data['status']='error';
        }
        return $data;
    }
    
    public function pinjam_notes()
    {
        $pinjam=PinjamNotes::with('pinjam')->with('user')->get();
        if($pinjam->count()!=0)
        {
            $data['data']=$pinjam;
            $data['status']='success';
        }
        else
        {
            $data['data']=array();
            $data['status']='error';
        }
        return $data;
    }
    public function pinjam_notes_by_pinjamid($id)
    {
        $pinjam=PinjamNotes::where('pinjam_id',$id)->with('pinjam')->with('user')->get();
        if($pinjam->count()!=0)
        {
            $data['data']=$pinjam;
            $data['status']='success';
        }
        else
        {
            $data['data']=array();
            $data['status']='error';
        }
        return $data;
    }
    public function pinjam_notes_by_userid($id)
    {
        $pinjam=PinjamNotes::where('pengguna_pic_pinjam_id',$id)->with('pinjam')->with('user')->get();
        if($pinjam->count()!=0)
        {
            $data['data']=$pinjam;
            $data['status']='success';
        }
        else
        {
            $data['data']=array();
            $data['status']='error';
        }
        return $data;
    }
    public function pinjam_rate()
    {
        $pinjam=PinjamRate::with('pinjam')->get();
        if($pinjam->count()!=0)
        {
            $data['data']=$pinjam;
            $data['status']='success';
        }
        else
        {
            $data['data']=array();
            $data['status']='error';
        }
        return $data;
    }
    public function pinjam_rate_by_pinjam($id)
    {
        $pinjam=PinjamRate::where('pinjam_id',$id)->with('pinjam')->get();
        if($pinjam->count()!=0)
        {
            $data['data']=$pinjam;
            $data['status']='success';
        }
        else
        {
            $data['data']=array();
            $data['status']='error';
        }
        return $data;
    }
    public function role()
    {
        $role=Role::get();
        if($role->count()!=0)
        {
            $data['data']=$role;
            $data['status']='success';
        }
        else
        {
            $data['data']=array();
            $data['status']='error';
        }
        return $data;
    }
    public function ruang()
    {
        $ruang=Ruang::with('eselon1')->with('pengguna')->get();
        $alat=AlatRuang::join('alat', 'alat.id', '=', 'alat_ruang.alat_id')
                ->select('alat.id as alat_id','alat.kapasitas as kapasitas_alat','alat.nama as nama_alat','alat.*','alat_ruang.alat_id as ar_id','alat_ruang.*')->get();
        // return $alat;
        $al=array();
        foreach($alat as $k=>$v)
        {
            $al[$v->ruang_id][]=$v;
        }
        if($ruang->count()!=0)
        {
            $rg=array();
            $x=0;
            foreach($ruang as $r=>$v)
            {
                $rg[$x]=$v;
                if(isset($al[$v->id]))
                    $rg[$x]['alat']=$al[$v->id];
                else
                    $rg[$x]['alat']=array();

                $x++;
            }
            $data['data']=$rg;
            $data['status']='success';
        }
        else
        {
            $data['data']=array();
            $data['status']='error';
        }
        return $data;
    }
    public function slider()
    {
        $slider=Slider::orderBy('created_at')->get();
        if($slider->count()!=0)
        {
            $data['data']=$slider;
            $data['status']='success';
        }
        else
        {
            $data['data']=array();
            $data['status']='error';
        }
        return $data;
    }
    public function user()
    {
        $user=User::with('eselon1')->with('eselon2')->with('role')->orderBy('created_at')->get();
        if($user->count()!=0)
        {
            $data['data']=$user;
            $data['status']='success';
        }
        else
        {
            $data['data']=array();
            $data['status']='error';
        }
        return $data;
    }
    
    public function update_profil(Request $req,$id)
    {
        $user=User::find($id);
        $simpan=0;

        $user->email = is_null($req->email) ? '-' : $req->email;
        $user->name = is_null($req->name) ? '-' : $req->name;
        $user->phone = is_null($req->phone) ? '-' : $req->phone;
        $user->username = is_null($req->username) ? '-' : $req->username;
        $user->nip = is_null($req->nip) ? '-' : $req->nip;
        $user->phone = is_null($req->phone) ? '-' : $req->phone;
        $c=$user->save();
        
        if($c)
            $simpan=1;

        if($simpan==1)
        {
            $data['data']=$user;
            $data['pesan']='Update Profil Berhasil';
            $data['status']='success';
        }
        else
        {
            $data['data']=array();
            $data['pesan']='Update Profil Gagal';
            $data['status']='error';
        }
        return $data;
    }
    
    public function changepassword(Request $req,$id)
    {
        $user=User::find($id);
        $simpan==0;

        $user->password = is_null($req->password) ? '-' : bcrypt($req->password);
        $user->password_real = is_null($req->password) ? '-' : $req->password;
        $c=$user->save();
        
        if($c)
            $simpan=1;

        if($simpan==1)
        {
            $data['data']=$user;
            $data['pesan']='Ubah Password  Berhasil';
            $data['status']='success';
        }
        else
        {
            $data['data']=array();
            $data['pesan']='Ubah Password Gagal';
            $data['status']='error';
        }
        return $data;
    }


    public function login(Request $request)
    {
        $user=$request->username;
        $pass=$request->password;
        $us=User::where('username',$user)->orWhere('email',$user)->with('eselon2')->with('role')->first();
        if($us)
        {
            if(Hash::check($pass,$us->password)) 
            {
                $us->token_firebase=$request->token;
                $us->save();

                $data['data']=$us;
                $data['status']='success';

                $notif=Notifikasi::where('user_id',$us->id)->where('read','=',false)->get();
                $data['notifcount']=$notif->count();
            } 
            else 
            {
                $data['data']=array();
                $data['status']='error';
                $data['notifcount']=0;
            }
            
        }
        else
        {
            $data['data']=array();
            $data['status']='error';
            $data['notifcount']=0;
        }

        return $data;
    }

    public function picruangan()
    {
        $us=User::where('role_id',2)->get();
        if($us)
        {
            $data['data']=$us;
            $data['status']='success';
        }
        else
        {
            $data['data']=array();
            $data['status']='error';
        }
        return $data;
    }
    public function simpannotes(Request $request,$idpinjam)
    {
        $pinjam=new PinjamNotes;
        $pinjam->notes = $request->notes;
        $pinjam->pinjam_id=$idpinjam;
        $pinjam->pengguna_pic_pinjam_id=$request->pengguna_pic_pinjam_id;
        if($request->hasFile('lampiran')) {
         
            $file=$request->file('lampiran');
            $filenamewithextension = $request->file('lampiran')->getClientOriginalName(); 
            $filename = pathinfo($filenamewithextension, PATHINFO_FILENAME);
            $extension = $request->file('lampiran')->getClientOriginalExtension();
            $filenametostore = $filename.'_'.uniqid().'.'.$extension;
            $path='sirangga/src/main/resources/uploads/lampiran/'.$filenametostore;
            // Storage::disk('sftp')->put($path, fopen($request->file('undangan'), 'r+'));
            $tujuan_upload='lampiran/';
            $pinjam->lampiran='lampiran/'.$filenametostore;
            $file->move($tujuan_upload,$filenametostore);
            
        }
        $c=$pinjam->save();
        if($c)
        {
            $id=$pinjam->id;
            $pjm=Pinjam::find($idpinjam);
            $pjm->pengguna_pic_pinjam_id = $request->pengguna_pic_pinjam_id;
            $pjm->pinjam_notes_id=$id;
            $pjm->save();
            $data['pesan']='Insert Data Notes Peminjaman  Berhasil';
            $data['status']='success';
        }
        else
        {
            $data['pesan']='Insert Data Notes Peminjaman  Gagal';
            $data['status']='error';
        }
        return $data;
    }
    public function simpanpinjamruang(Request $request,$iduser)
    {
        $pinjam=new Pinjam;
        $pinjam->users_peminjam_id=$iduser;
        $pinjam->ruang_id=$ruang_id=is_null($request->ruang_id) ? '-' : $request->ruang_id;
        $pinjam->file=is_null($request->file) ? '-' : $request->file;
        $pinjam->keterangan=is_null($request->keterangan) ? '-' : $request->keterangan;
        $pinjam->mulai=$mulai=is_null($request->mulai) ? '-' : $request->mulai;
        $pinjam->selesai=$selesai=is_null($request->selesai) ? '-' : $request->selesai;
        $pinjam->topik=is_null($request->topik) ? '-' : $request->topik;
        $pinjam->layout=is_null($request->layout) ? '-' : $request->layout;
        $pinjam->status=is_null($request->status) ? 0 : $request->status;
        $pinjam->undangan=is_null($request->undangan) ? '-' : $request->undangan;
        $pinjam->pinjam_notes_id=is_null($request->pinjam_notes_id) ? NULL : $request->pinjam_notes_id;
        $pinjam->jumlah_peserta=is_null($request->jumlah_peserta) ? 0 : $request->jumlah_peserta;
        $pinjam->pengguna_pic_pinjam_id=is_null($request->pengguna_pic_pinjam_id) ? NULL : $request->pengguna_pic_pinjam_id;
        $pinjam->pinjam_rate_id=is_null($request->pinjam_rate_id) ? NULL : $request->pinjam_rate_id;
        $pinjam->rating=is_null($request->rating) ? false : $request->rating;
        $pinjam->rate=is_null($request->rating) ? 0 : $request->rating;
        $pinjam->pimpinan_rapat=is_null($request->pimpinan_rapat) ? '-' : $request->pimpinan_rapat;

        if($request->hasFile('undangan')) {
         
            //get filename with extension
            $file=$request->file('undangan');
            $filenamewithextension = $request->file('undangan')->getClientOriginalName(); 
            //get filename without extension
            $filename = pathinfo($filenamewithextension, PATHINFO_FILENAME);
            //get file extension
            $extension = $request->file('undangan')->getClientOriginalExtension();
            //filename to store
            $filenametostore = $filename.'_'.uniqid().'.'.$extension;
            //Upload File to external server
            $path='sirangga/src/main/resources/uploads/undangan/'.$filenametostore;
            // Storage::disk('sftp')->put($path, fopen($request->file('undangan'), 'r+'));
            $tujuan_upload='undangan/';
            $pinjam->undangan='undangan/'.$filenametostore;
            $file->move($tujuan_upload,$filenametostore);
            //Store $filenametostore in the database
        }
        $c=$pinjam->save();

        $idpinjam=$pinjam->id;

        // $json='[{"nama": "Sound system rapat", "idalat": "40", "jumlah": "0", "keterangan":"" },
        //         {"nama": "Infokus","idalat": "44", "jumlah": "2", "keterangan": "test"}, 
        //         {"nama": "xxx","idalat": "45", "jumlah": "22", "keterangan": "test"}]';
        
        $idalat=$request->alat;
        $d=array();
        if($idalat!='')
        {
            $d=json_decode($idalat);
            if(count($d)!=0)
            {
                foreach($d as $k=>$v)
                {
                    $alat=new PinjamAlat;
                    // $alat->created_at = date('Y-m-d H:i:s');
                    // $alat->updated_at = date('Y-m-d H:i:s');
                    $alat->jumlah = $v->jumlah;
                    $alat->alat_id = $v->idalat;
                    $alat->pinjam_id = $idpinjam;
                    $alat->keterangan = $v->keterangan;
                    $alat->save();
                    // echo '<br>';
                }
            }
        }

        if($c)
            $simpan=1;

        if($simpan==1)
        {
            $data['data']=$pinjam;
            $data['pesan']='Insert Data Peminjaman  Berhasil';
            $data['alat']=$d;
            $data['status']='success';

            $role=User::where('role_id',4)->get();

            $eselon=User::where('id',$iduser)->with('eselon2')->first();
            $nama_eselon=($eselon ? $eselon->eselon2->nama : '');
            foreach($role as $k=>$v)
            {
                $ruang=Ruang::find($ruang_id);
                $nama_ruang=($ruang ? $ruang->nama : '');

                $notif=new Notifikasi;
                $notif->category =$title= 'Pinjam Ruang';
                $notif->message =$pesan= 'Pengajuan pinjaman ruang '.$nama_ruang.' oleh '.$nama_eselon.' pada tanggal '.date('d-m-Y',strtotime($mulai)).' s/d '.date('d-m-Y',strtotime($selesai));
                $notif->read = false;
                $notif->title = 'Verifikasi Peminjaman';
                $notif->user_id = $v->id;
                $notif->pinjam_id = $idpinjam;
                $notif->save();

                if($eselon->token_firebase!='')
                {
                    $data['hasil'][]=$this->sendFCM($title, $pesan, $v->token_firebase);
                }

            }

        }
        else
        {
            $data['data']=array();
            $data['pesan']='Insert Data Peminjaman Gagal';
            $data['alat']=array();
            $data['status']='error';
        }
        return $data;
        //PinjamAlat
        //PinjamNotes
        //PinjamRate
    }
    public function update_token($iduser,$tokenfirebase)
    {
        $user=User::find($iduser);
        $user->token_firebase=$tokenfirebase;
        // $user->updated_at=date('Y-m-d H:i:s');
        $user->save();
    }
    public function pesanan_pending()
    {
        $pinjam=Pinjam::where('status',0)->with('peminjam')->with('ruang')->with('pinjamnotes')->with('user')->with('pinjamalat')->orderBy('mulai','desc')->orderBy('selesai')->get();
        if($pinjam->count()!=0)
        {
           $pinj=array();
            $x=0;
            foreach($pinjam as $k=>$v)
            {
                
                $pinj[$x]=$v;
                $pinj[$x]['mulai']=date('d-m-Y H:i:s',strtotime($v->mulai));
                $pinj[$x]['selesai']=date('d-m-Y H:i:s',strtotime($v->selesai));
                // $pinj[$x]['rate']=floatval($v->rate);
                $pinjamAlat=PinjamAlat::where('pinjam_id',$v->id)->with('alat')->get();
                $xx=0;
                foreach($pinjamAlat as $ka=>$va)
                {
                    $pinj[$x]['pinjamalat'][$xx]['id']=$va->id;
                    $pinj[$x]['pinjamalat'][$xx]['created_at']=$va->created_at;
                    $pinj[$x]['pinjamalat'][$xx]['updated_at']=$va->updated_at;
                    $pinj[$x]['pinjamalat'][$xx]['jumlah']=$va->jumlah;
                    $pinj[$x]['pinjamalat'][$xx]['alat_id']=$va->alat_id;
                    $pinj[$x]['pinjamalat'][$xx]['pinjam_id']=$va->pinjam_id;
                    $pinj[$x]['pinjamalat'][$xx]['keterangan']=$va->keterangan;
                    $pinj[$x]['pinjamalat'][$xx]['nama']=$va->alat->nama;
                    $pinj[$x]['pinjamalat'][$xx]['kapasitas']=$va->alat->kapasitas;
                    $xx++;
                }
                $x++;
            }
            $data['data']=$pinjam;
            $data['status']='success';
        }
        else
        {
            $data['data']=array();
            $data['status']='error';
        }
        return $data;
    }
    public function list_notif()
    {
        
    }
    public function list_notif_by_user($id)
    {
        $notif=Notifikasi::where('user_id',$id)->with('pinjam')->with('user')->orderBy('created_at','desc')->get();
        if($notif->count()!=0)
        {
            $data['data']=$notif;
            $data['status']='success';
        }
        else
        {
            $data['data']=array();
            $data['status']='error';
        }
        return $data;
    }
    public function insert_notif(Request $request)
    {
        $notif=new Notifikasi;
        $notif->category = $request->category;
        $notif->message = $request->message;
        $notif->read = $request->read;
        $notif->title = $request->title;
        $notif->user_id = $request->user_id;
        $notif->pinjam_id = $request->pinjam_id;
        $c=$notif->save();
        if($c)
        {
            $data['pesan']='Simpan Notifikasi Berhasil';
            $data['status']='success';
        }
        else{
            $data['pesan']='Simpan Notifikasi Gagal';
            $data['status']='error';           
        }
        return $data;
    }
    public function update_notif($id)
    {
        $notif=Notifikasi::find($id);
        $notif->read=true;
        $c=$notif->save();
        if($c)
        {
            $data['pesan']='Update Notifikasi Berhasil';
            $data['status']='success';
        }
        else{
            $data['pesan']='Update Notifikasi Gagal';
            $data['status']='error';           
        }
        return $data;
    }
    public function delete_by_id($id)
    {
        $notif=Notifikasi::find($id);
        if($notif->delete())
        {
            $data['pesan']='Hapus Notifikasi Selesai';
            $data['status']='success';
        }
        else
        {
            $data['pesan']='Hapus Notifikasi Gagal';
            $data['status']='error';
        }
        return $data;
    }
    public function delete_all_by_user($iduser)
    {
        $notif=Notifikasi::where('user_id',$iduser);
        if($notif->delete())
        {
            $data['pesan']='Hapus Notifikasi Selesai';
            $data['status']='success';
        }
        else
        {
            $data['pesan']='Hapus Notifikasi Gagal';
            $data['status']='error';
        }
        return $data;
    }

    public function update_pemesanan($idpinjam,$status)
    {
        $pinjam=Pinjam::find($idpinjam);
        $users_peminjam_id=$pinjam->users_peminjam_id;
        $us=User::where('role_id',2)->get();
        if($us)
        {
            $data['picruangan']=$us;
        }
        else
        {
            $data['picruangan']=array();
        }
        if($pinjam)
        {
            $pinjam->status=$status;
            $c=$pinjam->save();
            if($c)
            {
                
                $data['pesan']='Update Peminjaman Berhasil';
                $data['status']='success';
                $userPeminjam=User::find($users_peminjam_id);
                // return $userPeminjam;
                $pesan=$hasil='';
                if($userPeminjam)
                {
                    $pesan='-';
                    if($userPeminjam->token_firebase!='')
                    {
                        $title='Informasi Pemesanan Ruangan';
                        if($status==1)
                        {
                            $pesan='Jadwal Pemesanan Ruangan Disetujui';
                            $hasil=$this->sendFCM($title, $pesan, $userPeminjam->token_firebase);
                        }
                        elseif($status==2)
                        {
                            $pesan='Jadwal Pemesanan Ruangan Di Tolak';
                            $hasil=$this->sendFCM($title, $pesan, $userPeminjam->token_firebase);
                        }
                        elseif($status==3)
                        {
                            $pesan='Jadwal Pemesanan Ruangan Di Batalkan';
                            $hasil=$this->sendFCM($title, $pesan, $userPeminjam->token_firebase);
                        }
                    }
                }
                $data['hasil']=$hasil;
                $data['status']=$status;
            }
            else{
                $data['pesan']='Update Peminjaman Gagal';
                $data['status']='error';           
            }
 
        }
        else
        {
            $data['pesan']='Data Pinjam Tidak Ditemukan';
            $data['status']='error'; 
        }
       
        return $data;
    }
    // public function pindah()
    public function pindah($file)
    {
        // $fileContents='Hello World';
        // Storage::disk('sftp')->put('text2.txt', $fileContents);
        // $filepath = public_path($file);
        // // Storage::disk('ftp')->put($dir.'/'.$name, fopen($filepath, 'r+'));
        $path='sirangga/src/main/resources/uploads/undangan/'.$file;
        Storage::disk('sftp')->put($path, fopen($filepath, 'r+'));
    }

    public function simpan_rate(Request $request,$idpinjam)
    {
        $pinjam=Pinjam::find($idpinjam);
        $rate=new PinjamRate;
        $rate->rate=$request->rate;
        $rate->ulasan=$request->ulasan;
        $rate->pinjam_id=$idpinjam;
        $c=$rate->save();

        $pinjam->rate=$request->rate;
        $pinjam->rating=true;
        $pinjam->save();

        if($c)
        {      
            $data['pesan']='Input Rate Berhasil';
            $data['status']='success';
        }
        else{
            $data['pesan']='Input Rate Gagal';
            $data['status']='error';           
        }

        return $data;
    }
    function sendFCM($title, $message, $firebasedevicetoken)
    {
        $curl = curl_init();
        $notification = [
            'title' => $title,
            'body' => $message,
            'image' => 'https://i.ibb.co/44WXhSX/sirangga.png'
        ];
        $extraNotificationData = ["message" => $notification, "moredata" =>'dd'];
        $fcmNotification = [
            //'registration_ids' => $tokenList, //multple token array
            'to'        => $firebasedevicetoken, //single token
            'notification' => $notification,
        ];
        $fields = json_encode ( $fcmNotification );
        curl_setopt_array($curl, array(
        CURLOPT_URL => "https://fcm.googleapis.com/fcm/send",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => $fields,
        CURLOPT_HTTPHEADER => array(
            "Authorization: key=AAAAAsROU-4:APA91bEfAfVd0tIJll9dUfOfvl3WOjblXSB7TfDDxH-Y1NXZI06dlN1gtvWPIzic6xH3na9eCVoZM7Nbe07KQ-MI7-rcIExRwWPrOzmPG-GlCm4-Qj7HRiZgYy-ZjKPlQtSBkEbt_Xqr",
            "Content-Type: application/json"
        ),
        ));
        
        $response = curl_exec($curl);
        $err = curl_error($curl);
        
        curl_close($curl);
        
        // if ($err) {
        // echo "cURL Error #:" . $err;
        // } else {
        // echo $response;
        // }
    }
   
    function sendFCMOld($title, $message, $firebasedevicetoken)
    {
        $curl = curl_init();
        $fields = [
            "notification" => [
                "title" => $title,
                "body" => $message,
                "image" => 'https://i.ibb.co/44WXhSX/sirangga.png'
                // "icon" => 'http://68.183.180.80/sirangga.png'
                // "icon" => 'http://68.183.180.80/sirangga-jpeg.jpg'
                // "icon" => 'http://sirangga.dephub.go.id/web/images/logo_bu_kemenhub.png'
            ],
            "to" => $firebasedevicetoken
        ];

        
        $fields = json_encode ( $fields );
        curl_setopt_array($curl, array(
        CURLOPT_URL => "https://fcm.googleapis.com/fcm/send",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => $fields,
        CURLOPT_HTTPHEADER => array(
            "authorization: key=AAAAAsROU-4:APA91bEfAfVd0tIJll9dUfOfvl3WOjblXSB7TfDDxH-Y1NXZI06dlN1gtvWPIzic6xH3na9eCVoZM7Nbe07KQ-MI7-rcIExRwWPrOzmPG-GlCm4-Qj7HRiZgYy-ZjKPlQtSBkEbt_Xqr",
            "content-type: application/json"
        ),
        ));
        
        $response = curl_exec($curl);
        $err = curl_error($curl);
        
        curl_close($curl);
        
        // if ($err) {
        // return "cURL Error #:" . $err;
        // } else {
        // return $response;
        // }
    }

    public function getcountnotif($iduser)
    {
        $notif=Notifikasi::where('user_id',$iduser)->where('read','=',false)->get();
        return $notif->count();
    }
}
