namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    public function index()
    {
        // 1. Fetch the posts (paginated so the {{ $posts->links() }} works)
        $posts = Post::latest()->paginate(9);

        // 2. Fetch the archives for your dropdown
        $archives = Post::selectRaw('year(created_at) as year, monthname(created_at) as month, count(*) as post_count')
            ->groupBy('year', 'month')
            ->orderBy('year', 'desc')
            ->get();

        // 3. PASS THEM BOTH TO THE VIEW
        return view('posts.index', compact('posts', 'archives'));
    }
}
