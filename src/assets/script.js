$(document).ready(function () {
    $('.db').click(function () {
        let vm = $(this);

        $(this).next().toggle(function () {
            let content = vm.find("span.open-close-db");
            if (content.text() === '+') {
                content.text('-')
            } else {
                content.text('+')
            }
        });
    });

    $('.table span').click(function () {
        $('.table span').css('background', 'none');
        $(this).css('background', 'darkgray');

        $data = {};
        $data['dbName'] = $(this).attr('data-dbName');
        $data['tableName'] = $(this).attr('data-tableName');

        let url = location.protocol + "//" + location.hostname + ":" + location.port + `/?dbName=${$data['dbName']}&tableName=${$data['tableName']}`;
        history.pushState("", "", url);

        $data = JSON.stringify($data);

        $.ajax({
            data: $data,
            method: "POST",
            url: '/',
        }).done(function (response) {
            let result = JSON.parse(response);

            let table;
            if (result['fields']) {
                $th = '';
                table = '';

                for (let i = 0; i < result['fields'].length; i++) {
                    $th += `<th>${result['fields'][i]}</th>`;
                }

                table = `<table class="table table-bordered">
                                <thead>${$th}<thead/>
                            </table>`;
            } else {
                $th = '<th>Options</th>';
                $trs = '';
                $tr = '';
                table = '';

                for (let key in result[0]) {
                    $th += `<th>${key}</th>`;
                }

                for (let i = 0; i < result.length; i++) {
                    $tds = '';

                    for (let key in result[i]) {
                        $tds += `<td>${result[i][key]}</td>`;
                    }

                    $trs += `<tr>
                                <td>
                                    <button class="btn edit-item" data-id="${result[i]['id']}">Edit</button>
                                    <button class="btn delete-item" data-id="${result[i]['id']}">Delete</button>
                                </td>
                                ${$tds}
                            </tr>`;
                }

                table = `<table class="table table-bordered">
                                <thead>${$th}<thead/>
                                <tbody>${$trs}</tbody>
                            </table>`;
            }

            $('.data-table').html(table)

        }).fail(function (error) {
            console.log(error)
        });
    });

    $(document).on('click', '.delete-item', function (e) {
        $data = {};
        $id = $(this).data("id");
        $data['dbName'] = getUrlParameter('dbName');
        $data['tableName'] = getUrlParameter('tableName');
        $data['id'] = $id;

        if (confirm('Do you really want to execute "DELETE FROM `' + $data['tableName'] + '` WHERE `' + $data['tableName'] + ".id`=" + $id + '" ?')) {
            $.ajax({
                data: $data,
                method: 'POST',
                //type: 'DELETE',
                url: '/delete',
            }).done(function (response) {
                let result = JSON.parse(response);

                let table;
                if (result['fields']) {
                    $th = '';
                    table = '';

                    for (let i = 0; i < result['fields'].length; i++) {
                        $th += `<th>${result['fields'][i]}</th>`;
                    }

                    table = `<table class="table table-bordered">
                                <thead>${$th}<thead/>
                            </table>`;
                } else {
                    $th = '<th>Options</th>';
                    $trs = '';
                    $tr = '';
                    table = '';

                    for (let key in result[0]) {
                        $th += `<th>${key}</th>`;
                    }

                    for (let i = 0; i < result.length; i++) {
                        $tds = '';

                        for (let key in result[i]) {
                            $tds += `<td>${result[i][key]}</td>`;
                        }

                        $trs += `<tr>
                                <td>
                                    <button class="btn edit-item" data-id="${result[i]['id']}">Edit</button>
                                    <button class="btn delete-item" data-id="${result[i]['id']}">Delete</button>
                                </td>
                                ${$tds}
                            </tr>`;
                    }

                    table = `<table class="table table-bordered">
                                <thead>${$th}<thead/>
                                <tbody>${$trs}</tbody>
                            </table>`;
                }

                $('.data-table').html(table);
            }).fail(function (error) {
                console.log(error)
            });

        } else {
            return false;
        }
    });

    function getUrlParameter(sParam) {
        let sPageURL = window.location.search.substring(1),
            sURLVariables = sPageURL.split('&'),
            sParameterName,
            i;

        for (i = 0; i < sURLVariables.length; i++) {
            sParameterName = sURLVariables[i].split('=');

            if (sParameterName[0] === sParam) {
                return typeof sParameterName[1] === undefined ? true : decodeURIComponent(sParameterName[1]);
            }
        }
        return false;
    }

    $('.create-db').click(function () {
        let url = location.protocol + "//" + location.hostname + ":" + location.port + '/create_db';
        history.pushState("", "", url);

        $content = "<div class='db-creation-form col-md-6'> " +
            "<div class='form-group mt-5'>" +
            "<label>Create Database</label>" +
            "<input type='text' class='form-control' placeholder='Database name'>" +
            "</div>" +
            "<button type='button' class='btn btn-secondary rounded-pill store-db'>Create</button>" +
            "</div>";

        $('.data-table').html($content);
    })

    $(document).on('click', '.store-db', function () {
        $.ajax({
            data: {
                dbName: $('.db-creation-form input').val(),
            },
            method: 'POST',
            url: '/store_db',
        }).done(function (response) {
            $('.db-creation-form input').val('');
        }).fail(function (error) {
            console.log(error)
        });
    });

    $(document).on('click', '.edit-item', function () {
        $data = {};
        $id = $(this).data("id");
        $data['dbName'] = getUrlParameter('dbName');
        $data['tableName'] = getUrlParameter('tableName');
        $data['id'] = $id;

        let url = location.protocol + '//' + location.hostname + ":" + location.port + `/?dbName=${$data['dbName']}&tableName=${$data['tableName']}` + `&id=${$id}`;
        history.pushState("", "", url);

        $.ajax({
            data: $data,
            method: 'GET',
            url: '/edit',
        }).done(function (response) {
            let result = JSON.parse(response);

            $trs = '';
            $tr = '';
            $table = '';
            $content = '';

            for (let key in result) {
                $trs += `<tr>
                            <td>${key}</td>
                            <td>
                                <input typeof="text" class="form-control" value="${result[key]}" data-key="${key}">
                            </td>
                        </tr>`;
            }

            $table = `<table class="table table-bordered">
                                <thead>
                                    <th>Column</th>
                                    <th>Value</th>
                                </thead>
                                <tbody>${$trs}</tbody>
                            </table>`;

            $content += $table + "<button type='button' class='btn btn-secondary rounded-pill float-right update-item'>Go</button>"

            $('.data-table').html($content);
        }).fail(function (error) {
            console.log(error)
        });
    });

    $(document).on('click', '.update-item', function () {
        $data = {};
        $values = {};

        $(".form-control:input").each(function () {
            $values[$(this).data('key')] = $(this).val();
        });

        $data['values'] = $values;
        $data['dbName'] = getUrlParameter('dbName');
        $data['tableName'] = getUrlParameter('tableName');
        $data['id'] = getUrlParameter('id');

        $.ajax({
            data: JSON.stringify($data),
            method: 'POST',
            url: '/update',
        }).done(function (response) {
            let url = location.protocol + '//' + location.hostname + ":" + location.port + `/?dbName=${$data['dbName']}&tableName=${$data['tableName']}`;
            history.pushState("", "", url);

            let result = JSON.parse(response);

            console.log(result)

            $th = '<th>Options</th>';
            $trs = '';
            $tr = '';
            $table = '';

            for (let key in result[0]) {
                $th += `<th>${key}</th>`;
            }

            for (let i = 0; i < result.length; i++) {
                $tds = '';

                for (let key in result[i]) {
                    $tds += `<td>${result[i][key]}</td>`;
                }

                $trs += `<tr>
                                <td>
                                    <button class="btn edit-item" data-id="${result[i]['id']}">Edit</button>
                                    <button class="btn delete-item" data-id="${result[i]['id']}">Delete</button>
                                </td>
                                ${$tds}
                            </tr>`;
            }

            $table = `<table class="table table-bordered">
                                <thead>${$th}<thead/>
                                <tbody>${$trs}</tbody>
                            </table>`;

            $('.data-table').html($table)

        }).fail(function (error) {
            console.log(error)
        });
    });

    $(document).on('click', '.create-item', function () {
        //tbl_insert?db=facebook_app&table=users

        $data = {};
        $data['dbName'] = getUrlParameter('dbName');
        $data['tableName'] = getUrlParameter('tableName');

        // let url = location.protocol + '//' + location.hostname + ":" + location.port + `/?dbName=${$data['dbName']}&tableName=${$data['tableName']}` + `&id=${$id}`;
        // history.pushState("", "", url);

        $.ajax({
            data: $data,
            method: 'GET',
            url: '/create_item',
        }).done(function (response) {
            let result = JSON.parse(response);

            $trs = '';
            $tr = '';
            $table = '';
            $content = '';

            console.log(result[0])

            for (let key in result) {
                $trs += `<tr>
                            <td>${result[key]}</td>
                            <td>
                                <input typeof="text" class="form-control" data-key="${result[key]}">
                            </td>
                        </tr>`;
            }

            $table = `<table class="table table-bordered">
                                <thead>
                                    <th>Column</th>
                                    <th>Value</th>
                                </thead>
                                <tbody>${$trs}</tbody>
                            </table>`;

            $content += $table + "<button type='button' class='btn btn-secondary rounded-pill float-right insert-item'>Go</button>"

            $('.data-table').html($content);
        }).fail(function (error) {
            console.log(error)
        });
    })

    $(document).on('click', '.insert-item', function () {
        $data = {};
        $values = {};

        $(".form-control:input").each(function () {
            $values[$(this).data('key')] = $(this).val();
        });

        $data['values'] = $values;
        $data['dbName'] = getUrlParameter('dbName');
        $data['tableName'] = getUrlParameter('tableName');

        $.ajax({
            data: JSON.stringify($data),
            method: 'POST',
            url: '/store_item',
        }).done(function (response) {
            let url = location.protocol + '//' + location.hostname + ":" + location.port + `/?dbName=${$data['dbName']}&tableName=${$data['tableName']}`;
            history.pushState("", "", url);

            let result = JSON.parse(response);

            console.log(result)

            $th = '<th>Options</th>';
            $trs = '';
            $tr = '';
            $table = '';

            for (let key in result[0]) {
                $th += `<th>${key}</th>`;
            }

            for (let i = 0; i < result.length; i++) {
                $tds = '';

                for (let key in result[i]) {
                    $tds += `<td>${result[i][key]}</td>`;
                }

                $trs += `<tr>
                                <td>
                                    <button class="btn edit-item" data-id="${result[i]['id']}">Edit</button>
                                    <button class="btn delete-item" data-id="${result[i]['id']}">Delete</button>
                                </td>
                                ${$tds}
                            </tr>`;
            }

            $table = `<table class="table table-bordered">
                                <thead>${$th}<thead/>
                                <tbody>${$trs}</tbody>
                            </table>`;

            $('.data-table').html($table)

        }).fail(function (error) {
            console.log(error)
        });
    });
})