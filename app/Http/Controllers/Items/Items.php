<?php

namespace App\Http\Controllers\Items;

use App\Http\Controllers\Controller;
use App\Http\Requests\Item\Item as Request;
use App\Models\Item\Item;
use App\Models\Setting\Category;
use App\Models\Setting\Currency;
use App\Models\Setting\Tax;
use App\Traits\Uploads;
use App\Utilities\ImportFile;

class Items extends Controller
{
    use Uploads;

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $items = Item::with('category')->collect();

        $categories = Category::enabled()->type('item')->pluck('name', 'id')
            ->prepend(trans('general.all_type', ['type' => trans_choice('general.categories', 2)]), '');

        return view('items.items.index', compact('items', 'categories'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        $categories = Category::enabled()->type('item')->pluck('name', 'id');

        $taxes = Tax::enabled()->pluck('name', 'id');

        return view('items.items.create', compact('categories', 'taxes'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     *
     * @return Response
     */
    public function store(Request $request)
    {
        $item = Item::create($request->input());

        // Upload picture
        if ($request->file('picture')) {
            $media = $this->getMedia($request->file('picture'), 'items');

            $item->attachMedia($media, 'picture');
        }

        $message = trans('messages.success.added', ['type' => trans_choice('general.items', 1)]);

        flash($message)->success();

        return redirect('items/items');
    }

    /**
     * Duplicate the specified resource.
     *
     * @param  Item  $item
     *
     * @return Response
     */
    public function duplicate(Item $item)
    {
        $clone = $item->duplicate();

        $message = trans('messages.success.duplicated', ['type' => trans_choice('general.items', 1)]);

        flash($message)->success();

        return redirect('items/items/' . $clone->id . '/edit');
    }

    /**
     * Import the specified resource.
     *
     * @param  ImportFile  $import
     *
     * @return Response
     */
    public function import(ImportFile $import)
    {
        $rows = $import->all();

        foreach ($rows as $row) {
            $data = $row->toArray();
            $data['company_id'] = session('company_id');

            Item::create($data);
        }

        $message = trans('messages.success.imported', ['type' => trans_choice('general.items', 2)]);

        flash($message)->success();

        return redirect('items/items');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  Item  $item
     *
     * @return Response
     */
    public function edit(Item $item)
    {
        $categories = Category::enabled()->type('item')->pluck('name', 'id');

        $taxes = Tax::enabled()->pluck('name', 'id');

        return view('items.items.edit', compact('item', 'categories', 'taxes'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Item  $item
     * @param  Request  $request
     *
     * @return Response
     */
    public function update(Item $item, Request $request)
    {
        $item->update($request->input());

        // Upload picture
        if ($request->file('picture')) {
            $media = $this->getMedia($request->file('picture'), 'items');

            $item->attachMedia($media, 'picture');
        }

        $message = trans('messages.success.updated', ['type' => trans_choice('general.items', 1)]);

        flash($message)->success();

        return redirect('items/items');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Item  $item
     *
     * @return Response
     */
    public function destroy(Item $item)
    {
        $relationships = $this->countRelationships($item, [
            'invoice_items' => 'invoices',
            'bill_items' => 'bills',
        ]);

        if (empty($relationships)) {
            $item->delete();

            $message = trans('messages.success.deleted', ['type' => trans_choice('general.items', 1)]);

            flash($message)->success();
        } else {
            $message = trans('messages.warning.deleted', ['name' => $item->name, 'text' => implode(', ', $relationships)]);

            flash($message)->warning();
        }

        return redirect('items/items');
    }

    public function autocomplete()
    {
        $type = request('type');
        $query = request('query');
        $currency_code = request('currency_code');

        if (empty($currency_code) || (strtolower($currency_code)  == 'null')) {
            $currency_code = setting('general.default_currency');
        }

        $currency = Currency::where('code', $currency_code)->first();

        $filter_data = array(
            'name' => $query
        );

        $items = Item::getItems($filter_data);

        if ($items) {
            foreach ($items as $item) {
                $tax = Tax::find($item->tax_id);

                $item_tax_price = 0;

                if (!empty($tax)) {
                    $item_tax_price = ($item->sale_price / 100) * $tax->rate;
                }

                $item->sale_price = $this->convertPrice($item->sale_price, $currency_code, $currency->rate);
                $item->purchase_price = $this->convertPrice($item->purchase_price, $currency_code, $currency->rate);

                switch ($type) {
                    case 'bill':
                        $total = $item->purchase_price + $item_tax_price;
                        break;
                    case 'invoice':
                    default:
                        $total = $item->sale_price + $item_tax_price;
                        break;
                }

                $item->total = money($total, $currency_code, true)->format();
            }
        }

        return response()->json($items);
    }

    public function totalItem()
    {
        $input_items = request('item');
        $currency_code = request('currency_code');

        if (empty($currency_code)) {
            $currency_code = setting('general.default_currency');
        }

        $json = new \stdClass;

        $sub_total = 0;
        $tax_total = 0;

        $items = array();

        if ($input_items) {
            foreach ($input_items as $key => $item) {
                $price = (double) $item['price'];
                $quantity = (int) $item['quantity'];

                $item_tax_total= 0;
                $item_sub_total = ($price * $quantity);

                if (!empty($item['tax_id'])) {
                    $tax = Tax::find($item['tax_id']);

                    $item_tax_total = (($price * $quantity) / 100) * $tax->rate;
                }

                $sub_total += $item_sub_total;
                $tax_total += $item_tax_total;

                $total = $item_sub_total + $item_tax_total;

                $items[$key] = money($total, $currency_code, true)->format();
            }
        }

        $json->items = $items;

        $json->sub_total = money($sub_total, $currency_code, true)->format();

        $json->tax_total = money($tax_total, $currency_code, true)->format();

        $grand_total = $sub_total + $tax_total;

        $json->grand_total = money($grand_total, $currency_code, true)->format();

        return response()->json($json);
    }

    protected function convertPrice($amount, $currency_code, $currency_rate, $format = false, $reverse = false)
    {
        $item = new Item();

        $item->amount = $amount;
        $item->currency_code = $currency_code;
        $item->currency_rate = $currency_rate;

        if ($reverse) {
            return $item->getReverseConvertedAmount($format);
        }

        return $item->getConvertedAmount($format);
    }
}
