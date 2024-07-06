<?php
global $list_user;
?>
<table>
    <tr>
        <th>ID</th>
        <th>Name</th>
        <th>Age</th>
        <th>Address</th>
    </tr>
<tbody>
<?php
foreach ($list_user as $list):
?>
    <tr>
        <td><?=$list->id?></td>
        <td><?=$list->name?></td>
        <td><?=$list->age?></td>
        <td><?=$list->address?></td>
    </tr>
<?php endforeach; ?>
</tbody>
</table>
