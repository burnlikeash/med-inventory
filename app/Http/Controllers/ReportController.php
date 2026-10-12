<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\DisposalRecord;
use App\Models\Equipment;
use App\Models\EquipmentCategory;
use App\Models\MaintenanceRecord;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    public function equipment(Request $request)
    {
        return view('reports.equipment', [
            'equipment' => $this->equipmentQuery($request)->orderBy('equipment_code')->get(),
            'departments' => Department::orderBy('name')->get(),
            'categories' => EquipmentCategory::orderBy('name')->get(),
        ]);
    }

    public function equipmentCsv(Request $request): StreamedResponse
    {
        $equipment = $this->equipmentQuery($request)->orderBy('equipment_code')->get();
        $filename = 'equipment_inventory_'.now()->format('Y-m-d_H-i-s').'.csv';

        return response()->streamDownload(function () use ($equipment) {
            $handle = fopen('php://output', 'w');

            fputcsv($handle, [
                'Equipment Code',
                'Barcode',
                'Name',
                'Category',
                'Brand',
                'Model',
                'Serial Number',
                'Property Number',
                'Department',
                'Location',
                'Operational Status',
                'Condition',
                'Lifecycle Status',
                'Purchase Date',
                'Purchase Price',
                'Supplier',
                'Warranty Expiry',
            ]);

            foreach ($equipment as $item) {
                fputcsv($handle, [
                    $item->equipment_code,
                    $item->barcode,
                    $item->name,
                    $item->category?->name,
                    $item->brand,
                    $item->model,
                    $item->serial_number,
                    $item->property_number,
                    $item->department?->name,
                    $item->location?->name,
                    $item->status,
                    $item->condition_status,
                    $item->lifecycle_status,
                    $item->purchase_date?->format('Y-m-d'),
                    $item->purchase_price,
                    $item->supplier,
                    $item->warranty_expiry?->format('Y-m-d'),
                ]);
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv',
        ]);
    }

    public function maintenance(Request $request)
    {
        $records = MaintenanceRecord::with(['equipment.department', 'equipment.location'])
            ->when($request->filled('maintenance_type'), fn (Builder $query) => $query->where('maintenance_type', $request->maintenance_type))
            ->when($request->filled('date_from'), fn (Builder $query) => $query->whereDate('maintenance_date', '>=', $request->date_from))
            ->when($request->filled('date_to'), fn (Builder $query) => $query->whereDate('maintenance_date', '<=', $request->date_to))
            ->orderByDesc('maintenance_date')
            ->get();

        return view('reports.maintenance', compact('records'));
    }

    public function disposal(Request $request)
    {
        $records = DisposalRecord::with(['equipment.department', 'equipment.location', 'approvedBy'])
            ->when($request->filled('disposal_method'), fn (Builder $query) => $query->where('disposal_method', $request->disposal_method))
            ->when($request->filled('date_from'), fn (Builder $query) => $query->whereDate('disposal_date', '>=', $request->date_from))
            ->when($request->filled('date_to'), fn (Builder $query) => $query->whereDate('disposal_date', '<=', $request->date_to))
            ->orderByDesc('disposal_date')
            ->get();

        return view('reports.disposal', compact('records'));
    }

    private function equipmentQuery(Request $request): Builder
    {
        return Equipment::with(['category', 'department', 'location'])
            ->filter($request->only([
                'department_id',
                'category_id',
                'status',
                'condition_status',
                'lifecycle_status',
            ]))
            ->when(! $request->boolean('include_archived'), fn (Builder $query) => $query->active());
    }
}
