var clickredaksi = 1
var nomor_tabel = 1
var k = 0


function updateRedaksi() {
    $.ajax({
        url: '/getRedaksi',
        type: 'GET',
        success: function (response) {
            var redaksi = [""]
            for (i = 0; i < response.length; i++) {
                redaksi += ("<option value='" + response[i].id + "'>" + response[i].nama_redaksi +
                    "</option>")
            }

            var table = document.getElementById('tabelRedaksi');
            table.setAttribute("style", "vertical-align: top !important;");
            clickredaksi++;

            var select1 = document.createElement("select");
            select1.innerHTML = "<option value='' selected disabled>Pilih Redaksi</option>" + redaksi;
            select1.setAttribute("id", "redaksi_id[" + clickredaksi + "]");
            select1.setAttribute("name", "redaksi_id");
            select1.setAttribute("class", "form-control input-default");
            select1.setAttribute("style", "height: 60px !important ; width: 200px !important ; word-wrap: normal !important; white-space: normal; overflow: hidden;   text-overflow: ellipsis;");
            select1.setAttribute("onchange", "change_redaksi(this)");
            select1.setAttribute("required", true);

            var input1 = document.createElement("p");
            // input1.setAttribute("type", "text");
            // input1.setAttribute("class", "form-control deskripsi_id");
            input1.setAttribute("id", "deskripsi_id[" + clickredaksi + "]");
            input1.setAttribute("name", "deskripsi_id");
            input1.setAttribute("style", "vertical-align: top; text-align: justify  !important");
            // input1.setAttribute("placeholder", "Deskripsi Redaksi");
            // input1.setAttribute("value", "");
            // input1.setAttribute("disabled", true);
            // input1.setAttribute("required", true);

            var input2 = document.createElement("ol");
            // input2.setAttribute("type", "text");
            // input2.setAttribute("class", "form-control sub_deskripsi_id");
            input2.setAttribute("id", "sub_deskripsi_id[" + clickredaksi + "]");
            input2.setAttribute("name", "sub_deskripsi_id");
            // input2.setAttribute("placeholder", "Sub Deskripsi Kosong");
            // input2.setAttribute("value", "");
            // input2.setAttribute("disabled", true);
            // input2.setAttribute("required", true);

            var button = document.createElement("button");
            button.innerHTML = "<i class='fa fa-trash'></i>";
            button.setAttribute("onclick", "deleteRow1(this)");
            button.setAttribute("class", "btn btn-danger shadow btn-xs sharp");

            var row = table.insertRow(-1);
            var cell1 = row.insertCell(0);
            var cell2 = row.insertCell(1);
            var cell3 = row.insertCell(2);
            var cell4 = row.insertCell(3);
            var cell5 = row.insertCell(4);

            cell1.innerHTML = "1";
            cell2.appendChild(select1);
            cell3.appendChild(input1);
            cell4.appendChild(input2);
            cell5.appendChild(button);



            reindex1();

        }
    });

}

function deleteRow1(r) {
    var table = r.parentNode.parentNode.rowIndex;
    document.getElementById("tabelRedaksi").deleteRow(table);
    clickredaksi--;

    var select_id_redaksi = document.querySelectorAll("#tabelRedaksi tr td:nth-child(2) select");
    for (var i = 0; i < select_id_redaksi.length; i++) {
        select_id_redaksi[i].id = "redaksi_id[" + (i + 1) + "]";
    }

    var select_id_deskripsi = document.querySelectorAll("#tabelRedaksi tr td:nth-child(3) p");
    for (var i = 0; i < select_id_deskripsi.length; i++) {
        select_id_deskripsi[i].id = "deskripsi_id[" + (i + 1) + "]";
    }

    var select_sub_deskripsi_id = document.querySelectorAll("#tabelRedaksi tr td:nth-child(4) ol");
    for (var i = 0; i < select_sub_deskripsi_id.length; i++) {
        select_sub_deskripsi_id[i].id = "sub_deskripsi_id[" + (i + 1) + "]";
    }

    reindex1();

    if (clickredaksi == 0) {
        updateRedaksi();
    }

}

function reindex1() {
    const ids = document.querySelectorAll("#tabelRedaksi tr > td:nth-child(1)");
    ids.forEach((e, i) => {
        e.innerHTML = "<strong id=nomor1[" + (i + 1) + "] value=" + (i + 1) + ">" + (i + 1) + "</strong>"
        nomor_tabel = i + 1;
    });
}

function change_redaksi(c) {
    var change = c.parentNode.parentNode.rowIndex;
    let token = $('#csrf').val();
    // alert(token);
    var redaksi_id = document.getElementById("redaksi_id[" + change + "]").value;
    // alert(redaksi_id);

    $.ajax({
        url: '/getDeskripsi',
        type: "POST",

        // "headers": { 'X-CSRF-TOKEN': $('meta[name="csrf_token"]').attr('content') },
        data: {
            'redaksi_id':  redaksi_id,
            '_token' : token,

                //   headers: {'X-CSRF-TOKEN': $('meta[name="csrf_token"]').attr('content'), '_method': 'patch'},

        },
        success: function (response) {
            document.getElementById("deskripsi_id[" + change + "]").innerHTML = response.deskripsi_redaksi;
            // document.getElementById("sub_deskripsi_id[" + change + "]").value = response.sub_deskripsi;


        }
    })
    $.ajax({
        url: '/getSubDeskripsi',
        type: "POST",

        // "headers": { 'X-CSRF-TOKEN': $('meta[name="csrf_token"]').attr('content') },
        data: {
            'redaksi_id':  redaksi_id,
            '_token' : token,

                //   headers: {'X-CSRF-TOKEN': $('meta[name="csrf_token"]').attr('content'), '_method': 'patch'},

        },
        success: function (response) {
            // var sub_deskripsi = [""];
            document.getElementById("sub_deskripsi_id["+ change +"]").innerHTML = "";
            var ordered_list = document.getElementById("sub_deskripsi_id["+ change +"]");
            // var ordered_list = document.createElement("ol");
            // ordered_list.setAttribute("type", "a");
            // p.append(ordered_list);
            // console.log(ordered_list);
            console.log(response.length);
            console.log(response[0].sub_deskripsi);
            // document.getElementById("deskripsi_id[" + change + "]").innerHTML = response.deskripsi_redaksi;
            for (i = 0; i < response.length; i++) {
                if(response[i].sub_deskripsi === null){
                    var list_item = document.createElement("li");
                    ordered_list.append(list_item);
                    list_item.innerHTML = "Tidak Ada Sub Deskripsi";
                }
                else{
                    var list_item = document.createElement("li");
                    ordered_list.append(list_item);
                    list_item.innerHTML = i+1 + ". " + response[i].sub_deskripsi;
                }


                // sub_deskripsi += ("<li>" + response['sub_deskripsi'][i].sub_deskripsi

                //     "</li>")
            }
            // document.getElementById("sub_deskripsi_id[" + change + "]").value = response.sub_deskripsi;


        }
    })
}





