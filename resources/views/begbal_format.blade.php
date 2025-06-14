<table class="table table-bordsered table-hover mb-0" width="100%">
    <thead>
        <tr>
            <th align="center"><b>Item ID</b></th>
            <th align="center"><b>Item Description</b></th>
            <th align="center"><b>Remarks</b></th>
            <th align="center"><b>Quantity</b></th>
        </tr>
    </thead>
    <tbody>
        @foreach($begbal as $item)
            <tr>
                <td align="center">{{ $item->id }}</td>
                <td>{{ $item->item_description }}</td>
                <td align="center">begbal</td>
                <td align="center"></td>
            </tr>
        @endforeach
    </tbody>
</table>