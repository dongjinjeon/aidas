<table class="custom-table transaction-search-table">
    <thead>
        <tr>
            <th>SL</th>
            <th>Transaction ID</th>
            <th>Email</th>
            <th>Username</th> 
            <th>Amount</th>
            <th>Method</th>
            <th>Status</th>
            <th>Time</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($transactions  as $key => $item)
            <tr>
                <td>{{ $transactions->firstItem()+$loop->index}}</td>
                <td>{{ @$item->trx_id }}</td>
                <td>{{ @$item->tran_creator->email }}</td>
                <td>{{ @$item->tran_creator->username }}</td> 
                <td>{{ get_amount($item->request_amount,$item->request_currency,2) }}</td>
                <td><span class="text--info">{{ @$item->gateway_currency->name }}</span></td>
                <td><span class="{{ @$item->stringStatus->class }}">{{ @$item->stringStatus->value }}</span></td>
                <td>{{ @$item->created_at->format('d-m-y h:i:s A') }}</td>
                <td>
                    @include('admin.components.link.info-default',[
                        'href'          => setRoute('admin.money.out.details', $item->id),
                        'permission'    => "admin.money.out.details",
                    ])
                </td>
            </tr>
        @empty 
        @include('admin.components.alerts.empty',['colspan' => 10])
        @endforelse
    </tbody>
</table>