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
    public function pinjam_by_ruang($id)
    {
        $pinjam=Pinjam::where('ruang_id',$id)->with('peminjam')->with('ruang')->with('pinjamnotes')->with('user')->with('pinjamalat')->orderBy('mulai')->orderBy('selesai')->get();
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
    public function login(Request $request)
    {
        $user=$request->username;
        $pass=$request->password;
        $us=User::where('username',$user)->orWhere('email',$user)->first();
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
    }
}
