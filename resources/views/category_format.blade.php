<table class="table table-bordsered table-hover mb-0" width="100%">
    <thead>
        <tr>
            <th align="center"><b>Cat/Subcat ID</b></th>
            <th align="center"><b>Category/Subcategory Name</b></th>
            <th align="center"><b>Subcategory Prefix</b></th>
        </tr>
    </thead>
    <tbody>
        @foreach($item_category as $ic)
            <tr>
                <td align="center" style='background-color: #e5f26f'><b>{{ $ic->id }}</b></td>
                <td align="center" style='background-color: #e5f26f'><b>{{ $ic->category_name }}</b></td>
                <td style='background-color: #e5f26f'></td>
            </tr>
            @foreach($subcat AS $s)
                @if($ic->id==$s->item_category_id)
                <tr>
                    <td align="center">{{ $s->id }}</td>
                    <td align="center">{{ $s->subcat_name }}</td>
                    <td align="center">{{ $s->subcat_prefix }}</td>
                </tr>
                @endif
            @endforeach
        @endforeach
    </tbody>
</table>