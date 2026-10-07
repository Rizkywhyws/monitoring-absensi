<?php
namespace App\Http\Controllers\Admin;
use Illuminate\Support\Str;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Group;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
class TambahUserController extends Controller
{
    public function index()
    {
        $users=User::where('role','peserta_pkl')
            ->latest()
            ->get();
        return view('user.index',compact('users'));
    }
    public function create()
    {
        $kuotaRayon=[
            'nama_rayon'=>'Jember',
            'terpakai'=>User::where('unit_rayon','KP Jember')->count(),
            'kapasitas'=>24,
        ];
        return view('admin.tambahuser',[
            'userName'=>'Refangga Ardiansah',
            'initials'=>'RA',
            'nim'=>'240810101052',
            'periodeMagang'=>'01 Agu 2026 – 30 Sep 2026',
            'divisi'=>'Instalasi & Jaringan',
            'pembimbing'=>'Bpk. Hendra Kusuma',
            'kampus'=>'Politeknik Negeri Jember',
            'kuotaRayon'=>$kuotaRayon,
        ]);
    }
    public function store(Request $request)
    {
        $validated=$request->validate([
            'namaLengkap'=>['required','string','max:150'],
            'nimMahasiswa'=>['required','string','max:30','unique:users,nim'],
            'asalUniversitas'=>['required','string','max:150'],
            'emailPribadi'=>['required','email','max:150','unique:users,email_pribadi'],
            'nomorTelepon'=>['required','string','max:20'],
            'kelompokDivisi'=>['required','string','max:100'],
            'mentor'=>['required','string','max:150'],
            'password'=>['required','string','min:6','max:100'],
            'tanggalMulai'=>['required','date'],
            'tanggalSelesai'=>['required','date','after_or_equal:tanggalMulai'],
            'unitRayon'=>['required',Rule::in([
                'KP Jember',
                'KP Banyuwangi',
                'KP Probolinggo',
                'SBU Surabaya',
            ])],
        ],[
            'nimMahasiswa.unique'=>'NIM ini sudah terdaftar di sistem.',
            'emailPribadi.unique'=>'Email pribadi ini sudah terdaftar di sistem.',
            'tanggalSelesai.after_or_equal'=>'Tanggal selesai tidak boleh sebelum tanggal mulai.',
            'kelompokDivisi.required'=>'Kelompok/Divisi Penugasan wajib diisi.',
            'mentor.required'=>'Nama Mentor wajib diisi.',
            'password.required'=>'Password wajib diisi.',
            'password.min'=>'Password minimal 6 karakter.',
        ]);
        $nomorTelepon='+62'.ltrim(
            preg_replace('/[^0-9]/','',$validated['nomorTelepon']),
            '0'
        );
        $slugNama=Str::of($validated['namaLengkap'])
            ->lower()
            ->replaceMatches('/[^a-z0-9]+/','.')
            ->trim('.');
        $emailKorporat=$slugNama.'@intern.plniconplus.co.id';
        $counter=1;
        while(User::where('email_korporat',$emailKorporat)->exists()){
            $emailKorporat=$slugNama.$counter.'@intern.plniconplus.co.id';
            $counter++;
        }
        $group=Group::firstOrCreate(
            [
                'name'=>trim($validated['kelompokDivisi']),
            ],
            [
                'description'=>null,
                'status'=>'active',
            ]
        );
        $status='active';
        if(\Carbon\Carbon::parse($validated['tanggalSelesai'])->lt(today())){
            $status='inactive';
        }
        $user=User::create([
            'name'=>$validated['namaLengkap'],
            'nim'=>$validated['nimMahasiswa'],
            'asal_universitas'=>$validated['asalUniversitas'],
            'email_pribadi'=>$validated['emailPribadi'],
            'nomor_telepon'=>$nomorTelepon,
            'group_id'=>$group->id,
            'periode_mulai'=>$validated['tanggalMulai'],
            'periode_selesai'=>$validated['tanggalSelesai'],
            'unit_rayon'=>$validated['unitRayon'],
            'mentor'=>$validated['mentor'],
            'email_korporat'=>$emailKorporat,
            'email'=>$emailKorporat,
            'password'=>Hash::make($validated['password']),
            'role'=>'peserta_pkl',
            'status'=>$status,
        ]);
        return redirect()
            ->route('admin.daftar-user')
            ->with('success','User berhasil tersimpan.')
            ->with('kredensial',[
                'email_login'=>$emailKorporat,
                'password'=>$validated['password'],
            ]);
    }
}