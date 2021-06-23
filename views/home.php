<div class="sidenav">
    <div class="text-center">
        <button class="btn create-db">+ New</button>
    </div>

    <?php foreach ($dbs_tables as $index => $db): ?>
        <div class="database">
            <button class="btn w-100 db db-<?= $index ?>"><span class="open-close-db">+</span> <?= $db['name']; ?>
            </button>
            <div class="table tables-<?= $index ?> hide">
                <?php foreach ($db['tables'] as $table): ?>
                    <span data-dbName="<?= $db['name']; ?>"
                          data-tableName="<?= $table[0]; ?>">  <?= $table[0]; ?></span>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<div class="data-table w-100 mt-3">
    <?php
    $table = '';

    if ($table_data['fields']) {
        $th = '';
        $table = '';

        foreach ($table_data['fields'] as $key => $field) {
            $th .= "<th>" . $table_data['fields'][$key] . "</th>";
        }

        $table = "<table class='table table-bordered'>
                        <thead>$th<thead/>
                  </table>";
    } else {
        $th = '<th>Options</th>';
        $trs = '';
        $tr = '';
        $table = '';

        foreach ($table_data[0] as $field => $value) {
            $th .= "<th>$field</th>";
        }

        foreach ($table_data as $index => $data) {
            $tds = '';

            foreach ($data as $key => $d) {
                $tds .= "<td>$d</td>";
            }

            $trs .= "<tr>
                        <td>
                            <button class='btn edit-item' data-id=" . $data['id'] . ">Edit</button>
                            <button class='btn delete-item' data-id=" . $data['id'] . ">Delete</button>
                        </td>$tds
                    </tr>";
        }

        $table = "<table class='table table-bordered'>
                        <thead>$th</thead>
                        <tbody>$trs</tbody>
                  </table>";
    }

    echo $table;

    ?>
</div>

<script src="../../src/assets/script.js"></script>


