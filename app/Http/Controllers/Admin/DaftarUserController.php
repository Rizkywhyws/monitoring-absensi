<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Group;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
class DaftarUserController extends Controller
{
    public function index(Request $request)
    {
        $query=User::with('group')->where('role','peserta_pkl');
        if($request->filled('keyword')){
            $keyword=$request->keyword;
            $query->where(function($q) use($keyword){
                $q->where('name','like',"%{$keyword}%")
                  ->orWhere('nim','like',"%{$keyword}%")
                  ->orWhere('email','like',"%{$keyword}%");
            });
        }
        if($request->filled('status')){
            if($request->status==='aktif'){
                $query->where('status','active')
                    ->where(function($q){
                        $q->whereNull('periode_selesai')
                          ->orWhereDate('periode_selesai','>=',today());
                    });
            }
            if($request->status==='nonaktif'){
                $query->where(function($q){
                    $q->where('status','inactive')
                      ->orWhereDate('periode_selesai','<',today());
                });
            }
        }
        if($request->filled('divisi')){
            $query->whereHas('group',function($q) use($request){
                $q->where('name','like',"%{$request->divisi}%");
            });
        }
        if($request->filled('periode')){
            if($request->periode==='batch-2-2026'){
                $query->whereDate('periode_mulai','>=','2026-08-01')
                    ->whereDate('periode_selesai','<=','2027-01-31');
            }elseif($request->periode==='batch-1-2026'){
                $query->whereDate('periode_mulai','>=','2026-02-01')
                    ->whereDate('periode_selesai','<=','2026-07-31');
            }
        }
        $users=$query->latest()->paginate(6)->withQueryString();
        $groups=Group::where('status','active')
            ->orderBy('name')
            ->get();
        return view('admin.daftaruser',compact('users','groups'));
    }
    public function tambah()
    {
        $groups=Group::where('status','active')
            ->orderBy('name')
            ->get();
        return view('admin.tambahuser',compact('groups'));
    }
    public function simpan(Request $request)
    {
        $request->validate([
            'name'=>'required|string|max:255',
            'nim'=>'required|string|max:30|unique:users,nim',
            'email'=>'required|email|unique:users,email',
            'group_id'=>'nullable|exists:groups,id',
            'periode_mulai'=>'nullable|date',
            'periode_selesai'=>'nullable|date|after_or_equal:periode_mulai',
            'password'=>'required|min:6',
        ]);
        User::create([
            'name'=>$request->name,
            'nim'=>$request->nim,
            'email'=>$request->email,
            'group_id'=>$request->group_id,
            'periode_mulai'=>$request->periode_mulai,
            'periode_selesai'=>$request->periode_selesai,
            'status'=>'active',
            'role'=>'peserta_pkl',
            'password'=>Hash::make($request->password),
        ]);
        return redirect()->route('admin.daftar-user')
            ->with('success','User berhasil ditambahkan.');
    }
    public function detail($id)
    {
        $user=User::with('group')
            ->where('role','peserta_pkl')
            ->findOrFail($id);
        return view('admin.detail-user',compact('user'));
    }
    public function edit($id)
    {
        $user=User::with('group')
            ->where('role','peserta_pkl')
            ->findOrFail($id);
        $groups=Group::where('status','active')
            ->orderBy('name')
            ->get();
        return view('admin.edit-user',compact('user','groups'));
    }
    public function update(Request $request,$id)
    {
        $user=User::where('role','peserta_pkl')->findOrFail($id);
        $request->validate([
            'name'=>'required|string|max:255',
            'nim'=>'required|string|max:30|unique:users,nim,'.$id,
            'email'=>'required|email|max:255|unique:users,email,'.$id,
            'mentor'=>'nullable|string|max:255',
            'group_name'=>'nullable|string|max:100',
            'periode_mulai'=>'nullable|date',
            'periode_selesai'=>'nullable|date|after_or_equal:periode_mulai',
            'status'=>'required|in:active,inactive',
            'password'=>'nullable|string|min:6|max:100',
        ]);
        $status=$request->status;
        if($request->periode_selesai && \Carbon\Carbon::parse($request->periode_selesai)->lt(today())){
            $status='inactive';
        }
        $groupId=null;
        if($request->filled('group_name')){
            $group=Group::firstOrCreate(
                [
                    'name'=>trim($request->group_name)
                ],
                [
                    'description'=>null,
                    'status'=>'active'
                ]
            );
            $groupId=$group->id;
        }
        $data=[
            'name'=>$request->name,
            'nim'=>$request->nim,
            'email'=>$request->email,
            'mentor'=>$request->mentor,
            'group_id'=>$groupId,
            'periode_mulai'=>$request->periode_mulai,
            'periode_selesai'=>$request->periode_selesai,
            'status'=>$status,
        ];
        if($request->filled('password')){
            $data['password']=Hash::make($request->password);
        }
        $user->update($data);
        return redirect()->route('admin.daftar-user')
            ->with('success','Data user berhasil diperbarui.');
    }
    public function ubahKelompok(Request $request,$id)
    {
        $user=User::where('role','peserta_pkl')->findOrFail($id);
        $request->validate([
            'group_id'=>'required|exists:groups,id'
        ]);
        $user->update([
            'group_id'=>$request->group_id
        ]);
        return redirect()->route('admin.daftar-user')
            ->with('success','Kelompok user berhasil diubah.');
    }
    public function resetPassword(Request $request,$id)
    {
        $user=User::where('role','peserta_pkl')->findOrFail($id);
        $request->validate([
            'password'=>'required|min:6|confirmed'
        ]);
        $user->update([
            'password'=>Hash::make($request->password)
        ]);
        return redirect()->route('admin.daftar-user')
            ->with('success','Password user berhasil direset.');
    }
    public function nonaktifkan($id)
    {
        $user=User::where('role','peserta_pkl')->findOrFail($id);
        $user->update([
            'status'=>'inactive'
        ]);
        return redirect()->route('admin.daftar-user')
            ->with('success','Akun user berhasil dinonaktifkan.');
    }
    public function aktifkan($id)
    {
        $user=User::where('role','peserta_pkl')->findOrFail($id);
        if($user->periode_selesai && $user->periode_selesai->lt(today())){
            $user->update([
                'status'=>'inactive'
            ]);
            return redirect()->route('admin.daftar-user')
                ->with('error','User tidak dapat diaktifkan karena periode PKL sudah berakhir.');
        }
        $user->update([
            'status'=>'active'
        ]);
        return redirect()->route('admin.daftar-user')
            ->with('success','Akun user berhasil diaktifkan kembali.');
    }
    public function hapus($id)
    {
        $user=User::where('role','peserta_pkl')->findOrFail($id);
        $user->delete();
        return redirect()->route('admin.daftar-user')
            ->with('success','User berhasil dihapus.');
    }
}