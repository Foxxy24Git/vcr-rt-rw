<?php

namespace App\Http\Controllers\Reseller;

use App\Http\Controllers\Controller;
use App\Models\InternetPackage;
use App\Services\Package\PackageService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InternetPackageController extends Controller
{
    public function __construct(
        private readonly PackageService $packageService
    ) {}

    public function index(Request $request): View
    {
        $this->authorize('viewAny', InternetPackage::class);

        $search = $request->string('search')->toString();

        $packages = $this->packageService->getActiveForResellerPaginated(
            search: $search ?: null,
        );

        return view('reseller.packages.index', [
            'packages' => $packages,
            'search' => $search,
        ]);
    }
}
