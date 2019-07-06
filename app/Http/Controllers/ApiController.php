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
        $pinjam=Pinjam::with('peminjam')->with('ruang')->with('pinjamnotes')->with('user')->with('pinjamalat')->orderBy('mulai')->orderBy('selesai')->get();
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
        $pinjam=Pinjam::where('users_peminjam_id',$id)->with('peminjam')->with('ruang')->with('pinjamnotes')->with('user')->with('pinjamalat')->orderBy('mulai')->orderBy('selesai')->get();
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
    public function pinjam_by_ruang($id)
    {
        $pinjam=Pinjam::where('ruang_id',$id)->with('peminjam')->with('ruang')->with('pinjamnotes')->with('user')->with('pinjamalat')->orderBy('mulai')->orderBy('selesai')->get();
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
        // $pinjam=Pinjam::where('ruang_id',$id)->with('peminjam')->with('ruang')->with('pinjamnotes')->with('user')->with('pinjamalat')->orderBy('mulai')->orderBy('selesai')->get();
        $pinjam=Pinjam::with('peminjam')->with('ruang')->with('pinjamnotes')->with('user')->with('pinjamalat')->orderBy('mulai')->orderBy('selesai')->get();
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
                    $pjm[$x]['event'][$idx]['name']=$item->topik;
                    $pjm[$x]['event'][$idx]['waktu_mulai']=$mul[1];
                    $pjm[$x]['event'][$idx]['waktu_selesai']=$sl[1];
                    $pjm[$x]['event'][$idx]['tgl_selesai']=date('d-m-Y',strtotime(trim(strtok($item->selesai,' '))));
                    $pjm[$x]['event'][$idx]['ruang']=$item->ruang->nama;
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
        $pinjam=Pinjam::whereDate('mulai', $date)->where('ruang_id',$idruang)->with('peminjam')->with('ruang')->with('pinjamnotes')->with('user')->with('pinjamalat')->orderBy('mulai')->orderBy('selesai')->get();
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

    public function jadwal_by_status($iduser,$status)
    {
        $pinjam=Pinjam::where('users_peminjam_id',$iduser)->where('status',$status)->with('peminjam')->with('ruang')->with('pinjamnotes')->with('user')->with('pinjamalat')->orderBy('mulai')->orderBy('selesai')->get();
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
        $pinjam=Pinjam::whereBetween('mulai', [$date1, $date2])->with('peminjam')->with('ruang')->with('pinjamnotes')->with('user')->with('pinjamalat')->orderBy('mulai')->orderBy('selesai')->get();
        // $pinjam=Pinjam::with('peminjam')->with('ruang')->with('pinjamnotes')->with('user')->with('pinjamalat')->orderBy('mulai')->orderBy('selesai')->get();
        if($pinjam->count()!=0)
        {
            $pinj=array();
            $x=0;
            foreach($pinjam as $k=>$v)
            {
                $tgl=strtok($v->mulai,' ');
                $tgl2=strtok($v->selesai,' ');

                $period=$this->date_range($tgl, $tgl2, "+1 day", "Y-m-d");
                foreach($period as $pk=>$pv)
                {
                    // $pinj[$tgl][]=$v;
                    $pinj[$pv][]=$v;
                }
            }
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
                    $pjm[$x]['event'][$idx]['name']=$item->topik;
                    $pjm[$x]['event'][$idx]['waktu_mulai']=$mul[1];
                    $pjm[$x]['event'][$idx]['waktu_selesai']=$sl[1];
                    $pjm[$x]['event'][$idx]['tgl_selesai']=date('d-m-Y',strtotime(trim(strtok($item->selesai,' '))));
                    $pjm[$x]['event'][$idx]['ruang']=$item->ruang->nama;
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
        if($ruang->count()!=0)
        {
            $data['data']=$ruang;
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
        $us=User::where('username',$user)->orWhere('email',$user)->first();
        if($us)
        {
            if(Hash::check($pass,$us->password)) 
            {
                $data['data']=$us;
                $data['status']='success';
            } 
            else 
            {
                $data['data']=array();
                $data['status']='error';
            }
            
        }
        else
        {
            $data['data']=array();
            $data['status']='error';
        }

        return $data;
    }

    public function simpanpinjamruang(Request $request,$iduser)
    {
        $pinjam=new Pinjam;
        $pinjam->users_peminjam_id=$iduser;
        $pinjam->ruang_id=is_null($request->ruang_id) ? '-' : $request->ruang_id;
        $pinjam->file=is_null($request->file) ? '-' : $request->file;
        $pinjam->keterangan=is_null($request->keterangan) ? '-' : $request->keterangan;
        $pinjam->mulai=$mulai=is_null($request->mulai) ? '-' : $request->mulai;
        $pinjam->selesai=$selesai=is_null($request->selesai) ? '-' : $request->selesai;
        $pinjam->topik=is_null($request->topik) ? '-' : $request->topik;
        $pinjam->layout=is_null($request->layout) ? '-' : $request->layout;
        $pinjam->status=is_null($request->status) ? 0 : $request->status;
        $pinjam->undangan=is_null($request->undangan) ? '-' : $request->undangan;
        $pinjam->pinjam_notes_id=is_null($request->pinjam_notes_id) ? 0 : $request->pinjam_notes_id;
        $pinjam->jumlah_peserta=is_null($request->jumlah_peserta) ? 0 : $request->jumlah_peserta;
        $pinjam->pengguna_pic_pinjam_id=is_null($request->pengguna_pic_pinjam_id) ? 0 : $request->pengguna_pic_pinjam_id;
        $pinjam->pinjam_rate_id=is_null($request->pinjam_rate_id) ? 0 : $request->pinjam_rate_id;
        $pinjam->rating=is_null($request->rating) ? false : $request->rating;
        $pinjam->rate=is_null($request->rating) ? 0 : $request->rating;
        $pinjam->pimpinan_rapat=is_null($request->pimpinan_rapat) ? '-' : $request->pimpinan_rapat;
        $c=$pinjam->save();

        $idpinjam=$pinjam->id;

        if($c)
            $simpan=1;

        if($simpan==1)
        {
            $data['data']=$pinjam;
            $data['pesan']='Insert Data Peminjaman  Berhasil';
            $data['status']='success';

            $role=User::where('role_id',4)->get();

            $eselon=User::where('id',$iduser)->with('eselon2')->first();
            $nama_eselon=($eselon ? $eselon->eselon2->nama : '');
            foreach($role as $k=>$v)
            {
                $ruang=Ruang::find($ruang_id);
                $nama_ruang=($ruang ? $ruang->nama : '');

                $notif=new Notifikasi;
                $notif->caterory = 'Pinjam Ruang';
                $notif->message = 'Pengajuan pinjaman ruang '.$nama_ruang.' oleh '.$nama_eselon.' pada tanggal '.date('d-m-Y',strtotime($mulai)).' s/d '.date('d-m-Y',strtotime($selesai));
                $notif->read = false;
                $notif->title = 'Verifikasi Peminjaman';
                $notif->user_id = $iduser;
                $notif->pinjam_id = $idpinjam;
                $notif->save();
            }

        }
        else
        {
            $data['data']=array();
            $data['pesan']='Insert Data Peminjaman Gagal';
            $data['status']='error';
        }
        return $data;
        //PinjamAlat
        //PinjamNotes
        //PinjamRate
    }

    public function pesanan_pending()
    {
        $pinjam=Pinjam::where('status',0)->with('peminjam')->with('ruang')->with('pinjamnotes')->with('user')->with('pinjamalat')->orderBy('mulai','desc')->orderBy('selesai')->get();
        if($pinjam->count()!=1)
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
    public function list_notif()
    {
        
    }
    public function list_notif_by_user($id)
    {
        $notif=Notifikasi::where('user_id',$id)->with('pinjam')->with('user')->orderBy('created_at','desc')->get();
        if($notif->count()!=1)
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
        $notif->caterory = $request->caterory;
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
        $pinjam->status=$status;
        $c=$pinjam->save();
        if($c)
        {
            $data['pesan']='Update Peminjaman Berhasil';
            $data['status']='success';
        }
        else{
            $data['pesan']='Update Peminjaman Gagal';
            $data['status']='error';           
        }
        return $data;
    }
}
