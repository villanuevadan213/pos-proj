<?php

namespace App\Http\Controllers;

use App\Models\Audit;
use App\Models\Item;
use App\Models\Supplier;
use App\Models\Tracking;
use Illuminate\Http\Request;

class AuditController extends Controller
{
    public function index() {
        $audits = Audit::with(['tracking', 'item'])->orderBy('id','desc')->simplePaginate(10);

        return view('audits.index', [
            'audits' => $audits
        ]);
    }

    public function create() {
        return view('audits.create');
    }

    public function show(Audit $audit) {
        return view('audits.show', ['audit' => $audit]);
    }

    public function store(Request $request) {
        // Get the submitted data from the textarea
        $data = trim($request->input('audit_data'));
    
        // Split the data by line breaks
        $lines = explode("\n", $data);
    
        // Ensure at least 5 lines are entered
        if (count($lines) < 5) {
            return redirect()->back()->with('error', 'Invalid input. Please enter all required fields.');
        }
    
        // Initialize variables to hold the detected data
        $tracking = '';
        $serial = '';
        $basket = '';
        $productControl = '';
        $title = '';
    
        // Loop through the lines and try to detect each field
        foreach ($lines as $line) {
            $line = trim($line); // Clean the line
    
            // Detect tracking number (12 or more digits)
            if (preg_match('/^\d{12,}$/', $line)) {
                $tracking = $line;
            }
            // Detect serial number (16 characters, combination of capital letters and numbers)
            elseif (strlen($line) == 16 && preg_match('/^[A-Z0-9]+$/', $line)) {
                $serial = $line;
            }
            // Detect basket number (starts with "BKT")
            elseif (preg_match('/^BKT[a-zA-Z0-9]+$/', $line)) {
                $basket = $line;
            }
            // Detect product control number (starts with "PCN")
            elseif (preg_match('/^PCN[a-zA-Z0-9]+$/', $line)) {
                $productControl = $line;
            }
            // Detect title (exactly "TITLE")
            elseif (strcasecmp($line, 'TITLE') == 0) {
                $title = $line;
            }
            // If nothing matches, assume it's the title
            else {
                $title = $line;
            }
        }
    
        // Check the last 12 digits of the tracking number for matching records
        if ($tracking) {
            $last12Digits = substr($tracking, -12);  // Extract last 12 digits
    
            // Find a tracking record where tracking_no ends with the last 12 digits
            $trackingRecord = Tracking::where('tracking_no', 'like', '%' . $last12Digits)->first();
    
            if ($trackingRecord) {
                // If a tracking record exists, populate the audit data
                $audit = Audit::where('tracking_id', $trackingRecord->id)->first();
    
                if ($audit) {
                    // If the audit already has an associated item
                    if ($audit->item_id) {
                        $item = Item::find($audit->item_id);
    
                        // If the item exists but has no name, generate it based on the supplier name
                        if (!$item->name) {
                            $supplier = Supplier::find($item->supplier_id);
    
                            // Generate item name based on supplier
                            if (!$supplier->name) {
                                $supplier->name = 'Supplier ' . $supplier->id;
                                $supplier->save();  // Save the updated supplier
                            }
    
                            // Now generate item name based on the supplier
                            $item->name = 'Item '.rand(1, 999);
                            $item->save();
                        }
                    } else {
                        // If no item is associated with this audit, create a new Item
    
                        $supplierName = 'Supplier '.rand(0, 999); // Adjust as necessary
    
                        // Find or create the supplier
                        $supplier = Supplier::firstOrCreate(['name' => $supplierName]);
    
                        // Create new Item and associate the Supplier
                        $item = Item::create([
                            'name' => 'Item '.rand(1, 999),
                            'price' => '₱ '.rand(1, 5000),
                            'supplier_id' => $supplier->id,  // Associate the Supplier
                            'quantity' => rand(1, 100),      // Add random quantity between 1 and 100
                        ]);
    
                        // Update the audit with the new item_id
                        $audit->item_id = $item->id;
                        $audit->save();
                    }
    
                    // Update the audit record with the rest of the fields
                    $audit->status = 'Updated';
                    $audit->serial_no = $serial;
                    $audit->basket_no = $basket;
                    $audit->product_control_no = $productControl;
                    $audit->title = $title;
                    $audit->save();
    
                    return redirect('/audits')->with('success', 'Audit updated successfully!');
                } else {
                    // If the audit doesn't exist, create a new audit record
    
                    $supplierName = 'Supplier '.rand(0, 999); // Adjust as necessary
                    $supplier = Supplier::firstOrCreate(['name' => $supplierName]);
    
                    // Create new Item and associate the Supplier
                    $item = Item::create([
                        'name' => 'Item '.rand(1, 999),
                        'price' => '₱ '.rand(1, 5000),
                        'supplier_id' => $supplier->id,  // Associate the Supplier
                        'quantity' => rand(1, 100),      // Add random quantity between 1 and 100
                    ]);
    
                    // Create a new audit record with the new tracking ID and Item
                    $audit = new Audit();
                    $audit->title = $title;
                    $audit->product_control_no = $productControl;
                    $audit->basket_no = $basket;
                    $audit->serial_no = $serial;
                    $audit->status = 'Created';
                    $audit->tracking_id = $trackingRecord->id;
                    $audit->item_id = $item->id; // Associate the new Item
                    $audit->save();
    
                    return redirect('/audits')->with('success', 'Audit created successfully with new Item and existing tracking number!');
                }
            } else {
                // If no matching tracking number is found, create a new tracking number
                // Generate a random tracking number with a length of 34 digits
                $randomDigits = '';
                for ($i = 0; $i < 34 - strlen($tracking); $i++) {
                    $randomDigits .= rand(0, 9);  // Append random digits
                }
    
                // Combine the random digits with the provided tracking number
                $newTrackingNo = $randomDigits . $tracking;
    
                // Create new tracking record
                $trackingRecord = new Tracking();
                $trackingRecord->tracking_no = $newTrackingNo;
                $trackingRecord->save();
    
                // Create new Supplier if needed
                $supplierName = 'Supplier '.rand(0, 999); // Adjust as necessary
                $supplier = Supplier::firstOrCreate(['name' => $supplierName]);
    
                // Create new Item with the new Supplier
                $item = Item::create([
                    'name' => 'Item '.rand(1, 999),
                    'price' => '₱ '.rand(1, 5000),
                    'supplier_id' => $supplier->id,  // Associate with Supplier
                    'quantity' => rand(1, 100),      // Add random quantity between 1 and 100
                ]);
    
                // Create new audit record with the new tracking ID and Item
                $audit = new Audit();
                $audit->title = $title;
                $audit->product_control_no = $productControl;
                $audit->basket_no = $basket;
                $audit->serial_no = $serial;
                $audit->status = 'Created';
                $audit->tracking_id = $trackingRecord->id;
                $audit->item_id = $item->id; // Associate the new Item
                $audit->save();
    
                return redirect('/audits')->with('success', 'Audit created successfully with new tracking number, Item, and Supplier!');
            }
        }
    
        return redirect()->back()->with('error', 'Tracking number is missing.');
    }
    
    public function edit(Audit $audit) {
        return view('audits.edit', ['audit' => $audit]);
    }

    public function update(Audit $audit) {
        // Validate incoming request data
        request()->validate([
            'title' => ['required'],
            'product_control_no' => ['required'],
            'basket_no' => ['required'],
            'serial_no' => ['required'],
            'tracking_no' => ['required'],
        ]);
    
        // Find the tracking record by tracking_no
        $tracking = Tracking::where('tracking_no', request('tracking_no'))->first();
    
        if (!$tracking) {
            // If tracking number doesn't exist, return with an error
            return redirect()->back()->with('error', 'Tracking number not found.');
        }
    
        // Update the audit record with the new data
        $audit->update([
            'title' => request('title'),
            'product_control_no' => request('product_control_no'),
            'basket_no' => request('basket_no'),
            'serial_no' => request('serial_no'),
            'tracking_id' => $tracking->id, // Update the tracking_id in the audit
        ]);
    
        // Return to the audits page with a success message
        return redirect('/audits')->with('success', 'Audit updated successfully!');
    }

    public function destroy(Audit $audit) {
    }
}
