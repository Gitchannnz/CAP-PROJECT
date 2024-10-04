<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\Slide;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Notification;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Laravel\Facades\Image;
use App\Exports\TransactionExport;
use Maatwebsite\Excel\Facades\Excel;

class AdminController extends Controller
{
    public function index()
    {
        if (!Gate::any(['is-admin', 'is-staff'])) {
            abort(403, 'This action is unauthorized.');
        }

        $user = Auth::user();
        $middleNameInitial = !empty($user->middlename) ? strtoupper(substr($user->middlename, 0, 1)) . '.' : '';
        $fullName = trim("{$user->firstname} {$middleNameInitial} {$user->lastname}");
        $usertype = ucfirst($user->usertype);

        $orders = Order::orderBy('created_at', 'DESC')->take(10)->get();
        $notifications = Notification::orderBy('created_at', 'desc')->get();

        $dashboardDatas = DB::select("
            SELECT 
                SUM(total) AS TotalAmount,
                SUM(IF(status='pending', total, 0)) AS TotalPendingAmount,
                SUM(IF(status='delivered', total, 0)) AS TotalDeliveredAmount,
                SUM(IF(status='canceled', total, 0)) AS TotalCanceledAmount,
                COUNT(*) AS Total,
                SUM(IF(status='pending', 1, 0)) AS TotalPending,
                SUM(IF(status='delivered', 1, 0)) AS TotalDelivered,
                SUM(IF(status='canceled', 1, 0)) AS TotalCanceled
            FROM orders
        ");

        $monthlyDatas = DB::select("
            SELECT 
                M.id AS MonthNo, 
                M.name AS MonthName,
                IFNULL(D.TotalAmount, 0) AS TotalAmount,
                IFNULL(D.TotalPendingAmount, 0) AS TotalPendingAmount,
                IFNULL(D.TotalDeliveredAmount, 0) AS TotalDeliveredAmount,
                IFNULL(D.TotalCanceledAmount, 0) AS TotalCanceledAmount
            FROM month_names M
            LEFT JOIN (
                SELECT 
                    MONTH(created_at) AS MonthNo,
                    SUM(total) AS TotalAmount,
                    SUM(CASE WHEN status = 'pending' THEN total ELSE 0 END) AS TotalPendingAmount,
                    SUM(CASE WHEN status = 'delivered' THEN total ELSE 0 END) AS TotalDeliveredAmount,
                    SUM(CASE WHEN status = 'canceled' THEN total ELSE 0 END) AS TotalCanceledAmount
                FROM orders
                WHERE YEAR(created_at) = YEAR(NOW())
                GROUP BY MONTH(created_at)
            ) D ON D.MonthNo = M.id
            ORDER BY M.id
        ");
        $AmountM = implode(',', collect($monthlyDatas)->pluck('TotalAmount')->toArray());
        $PendingAmountM = implode(',', collect($monthlyDatas)->pluck('TotalPendingAmount')->toArray());
        $DeliveredAmountM = implode(',', collect($monthlyDatas)->pluck('TotalDeliveredAmount')->toArray());
        $CanceledAmountM = implode(',', collect($monthlyDatas)->pluck('TotalCanceledAmount')->toArray());

        $TotalAmount = collect($monthlyDatas)->sum('TotalAmount');
        $TotalPendingAmount = collect($monthlyDatas)->sum('TotalPendingAmount');
        $TotalDeliveredAmount = collect($monthlyDatas)->sum('TotalDeliveredAmount');
        $TotalCanceledAmount = collect($monthlyDatas)->sum('TotalCanceledAmount');


        return view('admin.index', compact(
            'orders',
            'dashboardDatas',
            'AmountM',
            'PendingAmountM',
            'DeliveredAmountM',
            'CanceledAmountM',
            'TotalAmount',
            'TotalPendingAmount',
            'TotalDeliveredAmount',
            'TotalCanceledAmount',
            'user',
            'fullName',
            'usertype',
            'notifications',
        ));
    }

    public function updatePicture(Request $request)
    {
        if (!Gate::any(['is-admin', 'is-staff'])) {
            abort(403, 'This action is unauthorized.');
        }

        $request->validate([
            'profile_picture' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $user = Auth::user();

        if ($request->hasFile('profile_picture')) {
            $image = $request->file('profile_picture');
            $imageName = time() . '.' . $image->getClientOriginalExtension();

            $image->move(public_path('uploads/avatars'), $imageName);

            $user->avatar = $imageName;
            $user->save();
        }

        return redirect()->back()->with('success', 'Profile picture updated successfully.');
    }

    public function brands()
    {
        if (!Gate::allows('is-admin')) {
            abort(403, 'This action is unauthorized.');
        }

        $brands = Brand::orderBy('id', 'DESC')->paginate(10);
        return view("admin.brands", compact('brands'));
    }

    public function add_brand()
    {
        if (!Gate::allows('is-admin')) {
            abort(403, 'This action is unauthorized.');
        }

        return view("admin.brand-add");
    }

    public function brand_store(Request $request)
    {
        if (!Gate::allows('is-admin')) {
            abort(403, 'This action is unauthorized.');
        }

        $request->validate([
            'name' => 'required',
            'slug' => 'required|unique:brands,slug',
            'image' => 'nullable|mimes:png,jpg,jpeg|max:2048'
        ]);

        $brand = new Brand();
        $brand->name = $request->name;
        $brand->slug = Str::slug($request->name);

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $file_extension = $image->extension();
            $file_name = Carbon::now()->timestamp . '.' . $file_extension;
            $this->GenerateBrandThumbnailsImage($image, $file_name);
            $brand->image = $file_name;
        } else {
            $brand->image = null;
        }

        $brand->save();
        return redirect()->route('admin.brands')->with('status', 'New brand has been added successfully!');
    }

    public function brand_edit($id)
    {
        if (!Gate::allows('is-admin')) {
            abort(403, 'This action is unauthorized.');
        }

        $brand = Brand::find($id);
        return view('admin.brand-edit', compact('brand'));
    }

    public function brand_update(Request $request)
    {
        if (!Gate::allows('is-admin')) {
            abort(403, 'This action is unauthorized.');
        }

        $request->validate([
            'name' => 'required',
            'slug' => 'required|unique:brands,slug,' . $request->id,
            'image' => 'mimes:png,jpg,jpeg|max:2048'
        ]);

        $brand = Brand::find($request->id);
        $brand->name = $request->name;
        $brand->slug = Str::slug($request->slug);
        if ($request->hasFile('image')) {
            if (File::exists(public_path('uploads/brands') . '/' . $brand->image)) {
                File::delete(public_path('uploads/brands') . '/' . $brand->image);
            }
            $image = $request->file('image');
            $file_extension = $request->file('image')->extension();
            $file_name = Carbon::now()->timestamp . '.' . $file_extension;
            $this->GenerateBrandThumbnailsImage($image, $file_name);
            $brand->image = $file_name;
        }
        $brand->save();
        return redirect()->route('admin.brands')->with('status', 'Brand has been updated successfully!');
    }

    public function GenerateBrandThumbnailsImage($image, $imageName)
    {
        if (!Gate::allows('is-admin')) {
            abort(403, 'This action is unauthorized.');
        }

        $destinationPath = public_path('uploads/brands');
        $img = Image::read($image->path());
        $img->cover(124, 124, "top");
        $img->resize(124, 124, function ($constraint) {
            $constraint->aspectRatio();
        })->save($destinationPath . '/' . $imageName);
    }

    public function brand_delete($id)
    {
        if (!Gate::allows('is-admin')) {
            abort(403, 'This action is unauthorized.');
        }

        $brand = Brand::find($id);
        if (File::exists(public_path('uploads/brands') . '/' . $brand->image)) {
            File::delete(public_path('uploads/brands') . '/' . $brand->image);
        }
        $brand->delete();
        return redirect()->route('admin.brands')->with('status', 'Brand has been deleted successfully!');
    }

    public function categories()
    {
        if (!Gate::allows('is-admin')) {
            abort(403, 'This action is unauthorized.');
        }

        $categories = Category::orderBy('id', 'DESC')->paginate(10);
        return view("admin.categories", compact('categories'));
    }

    public function category_add()
    {
        if (!Gate::allows('is-admin')) {
            abort(403, 'This action is unauthorized.');
        }

        return view("admin.category-add");
    }

    public function category_store(Request $request)
    {
        if (!Gate::allows('is-admin')) {
            abort(403, 'This action is unauthorized.');
        }

        $request->validate([
            'name' => 'required',
            'slug' => 'required|unique:categories,slug',
            'image' => 'nullable|mimes:png,jpg,jpeg|max:2048'
        ]);

        $category = new Category();
        $category->name = $request->name;
        $category->slug = Str::slug($request->name);

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $file_extension = $image->extension();
            $file_name = Carbon::now()->timestamp . '.' . $file_extension;
            $this->GenerateCategoryThumbnailsImage($image, $file_name);
            $category->image = $file_name;
        } else {
            $category->image = null;
        }

        $category->save();
        return redirect()->route('admin.categories')->with('status', 'New category has been added successfully!');
    }

    public function GenerateCategoryThumbnailsImage($image, $imageName)
    {
        if (!Gate::allows('is-admin')) {
            abort(403, 'This action is unauthorized.');
        }

        $destinationPath = public_path('uploads/categories');
        $img = Image::read($image->path());
        $img->cover(124, 124, "top");
        $img->resize(124, 124, function ($constraint) {
            $constraint->aspectRatio();
        })->save($destinationPath . '/' . $imageName);
    }

    public function category_edit($id)
    {
        if (!Gate::allows('is-admin')) {
            abort(403, 'This action is unauthorized.');
        }

        $category = Category::find($id);
        return view('admin.category-edit', compact('category'));
    }

    public function category_update(Request $request)
    {
        if (!Gate::allows('is-admin')) {
            abort(403, 'This action is unauthorized.');
        }

        $request->validate([
            'name' => 'required',
            'slug' => 'required|unique:categories,slug,' . $request->id,
            'image' => 'mimes:png,jpg,jpeg|max:2048'
        ]);

        $category = Category::find($request->id);
        $category->name = $request->name;
        $category->slug = Str::slug($request->slug);
        if ($request->hasFile('image')) {
            if (File::exists(public_path('uploads/categories') . '/' . $category->image)) {
                File::delete(public_path('uploads/categories') . '/' . $category->image);
            }
            $image = $request->file('image');
            $file_extension = $request->file('image')->extension();
            $file_name = Carbon::now()->timestamp . '.' . $file_extension;
            $this->GenerateCategoryThumbnailsImage($image, $file_name);
            $category->image = $file_name;
        }
        $category->save();
        return redirect()->route('admin.categories')->with('status', 'Category has been updated successfully!');
    }

    public function category_delete($id)
    {
        if (!Gate::allows('is-admin')) {
            abort(403, 'This action is unauthorized.');
        }

        $category = Category::find($id);
        if (File::exists(public_path('uploads/categories') . '/' . $category->image)) {
            File::delete(public_path('uploads/categories') . '/' . $category->image);
        }
        $category->delete();
        return redirect()->route('admin.categories')->with('status', 'Category has been deleted successfully!');
    }

    public function products()
    {
        if (!Gate::any(['is-admin', 'is-staff'])) {
            abort(403, 'This action is unauthorized.');
        }

        $products = Product::with(['category', 'brand'])->orderBy('created_at', 'DESC')->paginate(10);
        return view("admin.products", compact('products'));
    }

    public function product_add()
    {
        if (!Gate::allows('is-admin')) {
            abort(403, 'This action is unauthorized.');
        }

        $categories = Category::select('id', 'name')->orderBy('name')->get();
        $brands = Brand::select('id', 'name')->orderBy('name')->get();
        return view("admin.product-add", compact('categories', 'brands'));
    }

    public function product_store(Request $request)
    {
        if (!Gate::any(['is-admin', 'is-staff'])) {
            abort(403, 'This action is unauthorized.');
        }

        $request->validate([
            'name' => 'required',
            'slug' => 'required|unique:products,slug',
            'short_description' => 'required',
            'description' => 'required',
            'regular_price' => 'required|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0',
            'SKU' => 'required|unique:products,SKU',
            'stock_status' => 'required|in:instock,outofstock',
            'featured' => 'required|boolean',
            'quantity' => 'required|integer|min:1',
            'critical_level' => 'required|integer|min:0',
            'image' => 'required|mimes:png,jpg,jpeg|max:2048',
            'category_id' => 'required|exists:categories,id',
            'brand_id' => 'required|exists:brands,id',
            'images.*' => 'mimes:png,jpg,jpeg|max:2048',
        ]);

        $product = new Product();
        $product->name = $request->name;
        $product->slug = Str::slug($request->slug);
        $product->short_description = $request->short_description;
        $product->description = $request->description;
        $product->regular_price = $request->regular_price;
        $product->sale_price = $request->sale_price ? $request->sale_price : null;
        $product->SKU = $request->SKU;
        $product->stock_status = $request->stock_status;
        $product->featured = $request->featured;
        $product->quantity = $request->quantity;
        $product->critical_level = $request->critical_level;
        $product->category_id = $request->category_id;
        $product->brand_id = $request->brand_id;

        $current_timestamp = now()->timestamp;

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = $current_timestamp . '.' . $image->extension();
            $this->GenerateProductThumbnailsImage($image, $imageName);
            $product->image = $imageName;
        }

        $gallery_arr = [];
        if ($request->hasFile('images')) {
            $files = $request->file('images');
            foreach ($files as $key => $file) {
                $gfileName = $current_timestamp . '-' . ($key + 1) . '.' . $file->getClientOriginalExtension();
                $this->GenerateProductThumbnailsImage($file, $gfileName);
                $gallery_arr[] = $gfileName;
            }
        }
        $product->images = implode(',', $gallery_arr);
        $product->save();

        return redirect()->route('admin.products')->with('status', 'New product has been added successfully!');
    }

    public function GenerateProductThumbnailsImage($image, $imageName)
    {
        if (!Gate::any(['is-admin', 'is-staff'])) {
            abort(403, 'This action is unauthorized.');
        }

        $destinationPathThumbnail = public_path('uploads/products/thumbnails');
        $destinationPath = public_path('uploads/products');
        $img = Image::read($image->path());

        $img->cover(540, 689, "top");
        $img->resize(540, 689, function ($constraint) {
            $constraint->aspectRatio();
        })->save($destinationPath . '/' . $imageName);

        $img->resize(104, 104, function ($constraint) {
            $constraint->aspectRatio();
        })->save($destinationPathThumbnail . '/' . $imageName);
    }

    public function product_edit($id)
    {
        if (!Gate::any(['is-admin', 'is-staff'])) {
            abort(403, 'This action is unauthorized.');
        }

        $product = Product::find($id);
        $categories = Category::select('id', 'name')->orderBy('name')->get();
        $brands = Brand::select('id', 'name')->orderBy('name')->get();
        return view('admin.product-edit', compact('product', 'categories', 'brands'));
    }

    public function product_update(Request $request)
    {
        if (!Gate::any(['is-admin', 'is-staff'])) {
            abort(403, 'This action is unauthorized.');
        }

        $request->validate([
            'name' => 'required',
            'slug' => 'required|unique:products,slug,' . $request->id,
            'short_description' => 'required',
            'description' => 'required',
            'regular_price' => 'required',
            'sale_price' => 'nullable|numeric|min:0',
            'SKU' => 'required',
            'stock_status' => 'required',
            'featured' => 'required',
            'quantity' => 'required',
            'critical_level' => 'required|integer|min:0',
            'image' => 'mimes:png,jpg,jpeg|max:2048',
            'category_id' => 'required',
            'brand_id' => 'required',
        ]);

        $product = Product::find($request->id);
        $product->name = $request->name;
        $product->slug = Str::slug($request->name);
        $product->short_description = $request->short_description;
        $product->description = $request->description;
        $product->regular_price = $request->regular_price;
        $product->sale_price = ($request->sale_price && $request->sale_price !== 'N/A') ? $request->sale_price : null;
        $product->SKU = $request->SKU;
        $product->stock_status = $request->stock_status;
        $product->featured = $request->featured;
        $product->quantity = $request->quantity;
        $product->critical_level = $request->critical_level;
        $product->category_id = $request->category_id;
        $product->brand_id = $request->brand_id;

        $current_timestamp = Carbon::now()->timestamp;

        if ($request->hasFile('image')) {
            if (File::exists(public_path('uploads/products') . '/' . $product->image)) {
                File::delete(public_path('uploads/products') . '/' . $product->image);
            }
            if (File::exists(public_path('uploads/products/thumbnails') . '/' . $product->image)) {
                File::delete(public_path('uploads/products/thumbnails') . '/' . $product->image);
            }
            $image = $request->file('image');
            $imageName = $current_timestamp . '.' . $image->extension();
            $this->GenerateProductThumbnailsImage($image, $imageName);
            $product->image = $imageName;
        }

        $gallery_arr = [];
        $gallery_images = "";
        $counter = 1;

        if ($request->hasFile('images')) {
            foreach (explode(',', $product->images) as $ofile) {
                if (File::exists(public_path('uploads/products') . '/' . $ofile)) {
                    File::delete(public_path('uploads/products') . '/' . $ofile);
                }
                if (File::exists(public_path('uploads/products/thumbnails') . '/' . $ofile)) {
                    File::delete(public_path('uploads/products/thumbnails') . '/' . $ofile);
                }
            }

            $allowedfileExtension = ['jpg', 'png', 'jpeg'];
            $files = $request->file('images');
            foreach ($files as $file) {
                $gextension = $file->getClientOriginalExtension();
                $gcheck = in_array($gextension, $allowedfileExtension);
                if ($gcheck) {
                    $gfileName = $current_timestamp . "-" . $counter . "." . $gextension;
                    $this->GenerateProductThumbnailsImage($file, $gfileName);
                    array_push($gallery_arr, $gfileName);
                    $counter = $counter + 1;
                }
            }
            $gallery_images = implode(',', $gallery_arr);
            $product->images = $gallery_images;
        }
        $product->save();
        return redirect()->route('admin.products')->with('status', 'Product has been updated successfully!');
    }

    public function slides()
    {
        if (!Gate::allows('is-admin')) {
            abort(403, 'This action is unauthorized.');
        }

        $slides = Slide::orderBy('id', 'DESC')->paginate(12);
        return view('admin.slides', compact('slides'));
    }

    public function slide_add()
    {
        if (!Gate::allows('is-admin')) {
            abort(403, 'This action is unauthorized.');
        }

        return view('admin.slide-add');
    }

    public function slide_store(Request $request)
    {
        if (!Gate::allows('is-admin')) {
            abort(403, 'This action is unauthorized.');
        }

        $request->validate([
            'tagline' => 'required',
            'title' => 'required',
            'subtitle' => 'required',
            'link' => 'required',
            'status' => 'required',
            'image' => 'required|mimes:png,jpg,jpeg|max:2048'
        ]);
        $slide = new Slide();
        $slide->tagline = $request->tagline;
        $slide->title = $request->title;
        $slide->subtitle = $request->subtitle;
        $slide->link = $request->link;
        $slide->status = $request->status;

        $image = $request->file('image');
        $file_extension = $request->file('image')->extension();
        $file_name = Carbon::now()->timestamp . '.' . $file_extension;
        $this->GenerateSlideThumbnailsImage($image, $file_name);
        $slide->image = $file_name;
        $slide->save();
        return redirect()->route('admin.slides')->with("status", "Slide added successfully!");
    }

    public function GenerateSlideThumbnailsImage($image, $imageName)
    {
        if (!Gate::allows('is-admin')) {
            abort(403, 'This action is unauthorized.');
        }

        $destinationPath = public_path('uploads/slides');
        $img = Image::read($image->path());
        $img->cover(500, 600, "top");
        $img->resize(500, 600, function ($constraint) {
            $constraint->aspectRatio();
        })->save($destinationPath . '/' . $imageName);
    }

    public function slide_edit($id)
    {
        if (!Gate::allows('is-admin')) {
            abort(403, 'This action is unauthorized.');
        }

        $slide = Slide::find($id);
        return view('admin.slide-edit', compact('slide'));
    }

    public function slide_update(Request $request)
    {
        if (!Gate::allows('is-admin')) {
            abort(403, 'This action is unauthorized.');
        }

        $request->validate([
            'tagline' => 'required',
            'title' => 'required',
            'subtitle' => 'required',
            'link' => 'required',
            'status' => 'required',
            'image' => 'mimes:png,jpg,jpeg|max:2048'
        ]);
        $slide = Slide::find($request->id);
        $slide->tagline = $request->tagline;
        $slide->title = $request->title;
        $slide->subtitle = $request->subtitle;
        $slide->link = $request->link;
        $slide->status = $request->status;

        if ($request->hasFile('image')) {
            if (File::exists(public_path('uploads/slides') . '/' . $slide->image)) {
                File::delete(public_path('uploads/slides') . '/' . $slide->image);
            }
            $image = $request->file('image');
            $file_extension = $request->file('image')->extension();
            $file_name = Carbon::now()->timestamp . '.' . $file_extension;
            $this->GenerateSlideThumbnailsImage($image, $file_name);
            $slide->image = $file_name;
        }
        $slide->save();
        return redirect()->route('admin.slides')->with("status", "Slide updated successfully!");
    }

    public function slide_delete($id)
    {
        if (!Gate::allows('is-admin')) {
            abort(403, 'This action is unauthorized.');
        }

        $slide = Slide::find($id);

        if ($slide) {
            if (File::exists(public_path('uploads/slides/' . $slide->image))) {
                File::delete(public_path('uploads/slides/' . $slide->image));
            }
            $slide->delete();
        }

        return redirect()->route('admin.slides')->with("status", "Slide deleted successfully!");
    }

    public function search(Request $request)
    {
        if (!Gate::any(['is-admin', 'is-staff'])) {
            abort(403, 'This action is unauthorized.');
        }

        $query = $request->input('query');

        $products = Product::where('name', 'LIKE', "%{$query}%")
            ->orderByRaw("name LIKE '{$query}' DESC")
            ->take(8)
            ->get();

        $orders = Order::with('user')
            ->where(function ($q) use ($query) {
                $q->where('order_number', 'LIKE', "%{$query}%")
                    ->orWhereHas('user', function ($q) use ($query) {
                        $q->where('firstname', 'LIKE', "%{$query}%")
                            ->orWhere('lastname', 'LIKE', "%{$query}%")
                            ->orWhere('institutional_id', 'LIKE', "%{$query}%");
                    });
            })
            ->take(8)
            ->get(['id', 'order_number', 'name']);

        return response()->json([
            'products' => $products,
            'orders' => $orders,
        ]);
    }

    public function toggleStatus($id)
    {
        if (!Gate::allows('is-admin')) {
            abort(403, 'This action is unauthorized.');
        }

        $product = Product::findOrFail($id);
        $product->toggleStatus();

        return response()->json(['success' => true]);
    }

    public function staff(Request $request)
    {
        if (!Gate::allows('is-admin')) {
            abort(403, 'This action is unauthorized.');
        }

        $query = User::query()->where('usertype', 'STAFF');

        if ($request->filled('name')) {
            $query->where(function ($q) use ($request) {
                $q->where('firstname', 'like', '%' . $request->name . '%')
                    ->orWhere('lastname', 'like', '%' . $request->name . '%')
                    ->orWhere('email', 'like', '%' . $request->name . '%');
            });
        }

        $staff = $query->paginate(10);
        return view('admin.staff', compact('staff'));
    }

    public function store(Request $request)
    {
        if (!Gate::allows('is-admin')) {
            abort(403, 'This action is unauthorized.');
        }

        $validator = Validator::make($request->all(), [
            'firstname' => 'required|string|max:255',
            'lastname' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
        ]);

        if ($validator->fails()) {
            return redirect()->route('admin.staff')
                ->withErrors($validator)
                ->withInput();
        }

        try {
            User::create([
                'firstname' => $request->firstname,
                'lastname' => $request->lastname,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'usertype' => 'STAFF',
            ]);

            return redirect()->route('admin.staff')
                ->with('success', 'Staff account created successfully!');
        } catch (\Exception $e) {
            return redirect()->route('admin.staff')
                ->with('error', 'Failed to create staff account: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        if (!Gate::allows('is-admin')) {
            abort(403, 'This action is unauthorized.');
        }

        $user = User::findOrFail($id);
        return view('admin.staff-edit', compact('user'));
    }

    public function update(Request $request, $id)
    {
        if (!Gate::allows('is-admin')) {
            abort(403, 'This action is unauthorized.');
        }

        $validator = Validator::make($request->all(), [
            'firstname' => 'required|string|max:255',
            'lastname' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'password' => 'nullable|string|min:8',
        ]);

        if ($validator->fails()) {
            return redirect()->route('admin.staff.edit', $id)
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $user = User::findOrFail($id);
            $user->firstname = $request->firstname;
            $user->lastname = $request->lastname;
            $user->email = $request->email;
            if ($request->filled('password')) {
                $user->password = Hash::make($request->password);
            }
            $user->save();

            return redirect()->route('admin.staff')
                ->with('success', 'Staff account updated successfully!');
        } catch (\Exception $e) {
            return redirect()->route('admin.staff.edit', $id)
                ->with('error', 'Failed to update staff account: ' . $e->getMessage());
        }
    }

    public function transactions_history(Request $request)
    {
        $search = $request->input('search');
        $status = $request->input('status');

        $transactions = Transaction::with('order.user')
            ->when($search, function ($query) use ($search) {
                return $query->whereHas('order', function ($q) use ($search) {
                    $q->where('order_number', 'LIKE', "%{$search}%")
                        ->orWhereHas('user', function ($u) use ($search) {
                            $u->where('firstname', 'LIKE', "%{$search}%")
                                ->orWhere('lastname', 'LIKE', "%{$search}%");
                        });
                });
            })
            ->when($status, function ($query) use ($status) {
                return $query->where('status', $status);
            })
            ->paginate(10);

        return view('admin.transactions-history', compact('transactions', 'search', 'status'));
    }

    public function exportTransactions()
    {
        return Excel::download(new TransactionExport, 'reports.xlsx');
    }

    public function createNewOrderNotification($order_id)
    {
        $existingNotification = Notification::where('related_id', $order_id)
            ->where('type', 'order')
            ->where('is_read', false)
            ->first();
    
        if (!$existingNotification) {
            $order = Order::find($order_id);
            $orderNumber = $order->order_number ?? 'Order #' . $order_id;
    
            Notification::create([
                'type' => 'order',
                'related_id' => $order_id,
                'url' => route('admin.order.details', ['order_id' => $order_id]),
                'message' => "New Order placed: $orderNumber",
                'is_read' => false,
            ]);
        }
    }    
    
    public function createLowStockNotification($product_id)
    {
        $existingNotification = Notification::where('related_id', $product_id)
            ->where('type', 'product')
            ->where('is_read', false)
            ->first();
    
        if (!$existingNotification) {
            $product = Product::find($product_id);
    
            if ($product) {
                Notification::create([
                    'type' => 'product',
                    'related_id' => $product_id,
                    'url' => route('admin.products', ['product_id' => $product_id]),
                    'message' => "Low Stock Product: " . $product->name,
                    'is_read' => false,
                ]);
            }
        }
    }    
    
    public function markAsRead($id)
    {
        $notification = Notification::findOrFail($id);
        $notification->update(['is_read' => true]);
    
        return redirect($notification->getNotificationUrl());
    }
    
    public function settings()
    {
        return view('admin.settings');
    }

     public function settingsUpdate(Request $request)
{
   
    $request->validate([
        'firstname' => 'required|string|max:255',
        'middlename' => 'nullable|string|max:255',
        'lastname' => 'required|string|max:255',
        'email' => 'required|email|max:255',
        'old_password' => 'required|string',
        'new_password' => 'nullable|string|min:8|confirmed',
    ]);

  
    $user = auth()->user();

    
    if (!Hash::check($request->old_password, $user->password)) {
        return back()->withErrors(['old_password' => 'The provided password does not match your current password.']);
    }

    $user->firstname = $request->firstname;
    $user->middlename = $request->middlename;
    $user->lastname = $request->lastname;
    $user->email = $request->email;


    if ($request->new_password) {
        $user->password = Hash::make($request->new_password);
    }

    $user->save();

  
    return redirect()->back()->with('success', 'Settings updated successfully.');
}


}
