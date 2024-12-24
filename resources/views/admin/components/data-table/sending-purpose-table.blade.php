<table class="custom-table">
    <thead>
        <tr> 
            <th>Name</th>
            <th>Slug</th>  
            <th>Status</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        @forelse ($sending_purpose ?? [] as $item)
        <tr data-item="{{ $item->editData }}">
            <td>{{ @$item->name}}</td>
            <td>{{ @$item->slug}}</td> 
            <td>
                @include('admin.components.form.switcher',[
                    'name'          => 'category_status',
                    'value'         => $item->status,
                    'options'       => ['Enable' => 1,'Disable' => 0],
                    'onload'        => true,
                    'data_target'       => $item->id,
                    'permission'    => "admin.sending.purpose.status.update",
                ])
            </td>
            <td>
                @include('admin.components.link.edit-default',[
                    'href'          => "javascript:void(0)",
                    'class'         => "edit-modal-button",
                    'permission'    => "admin.sending.purpose.update",
                ]) 
                @include('admin.components.link.delete-default',[
                    'href'          => "javascript:void(0)",
                    'class'         => "delete-modal-button",
                    'permission'    => "admin.sending.purpose.delete",
                ]) 
            </td>
        </tr>
        @empty
            @include('admin.components.alerts.empty',['colspan' => 7])
        @endforelse
    </tbody>
</table> 