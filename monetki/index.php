<?php
$db = new mysqli("localhost", "root", "ServBay.dev", "monetki");
$db->set_charset("utf8");

$kraje_wynik = $db->query("SELECT * FROM kraje ORDER BY nazwa");
$metale_wynik = $db->query("SELECT * FROM metale ORDER BY nazwa");
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Kolekcja Monet</title>
    <style>
        table { width: 100%; border-collapse: collapse; font-family: sans-serif; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: center; }
        .form-box { background: #eee; padding: 20px; margin-top: 20px; }
        select, input { padding: 5px; margin: 5px; }
        .fl { border: 1px solid black; cursor: pointer; }
    </style>
</head>
<body onload="odswiez()">

    <table>
        <thead>
            <tr>
                <th>Flaga</th><th>Nominał</th><th>Nr Kat.</th><th>Stop</th><th>Rok</th><th>Akcja</th>
            </tr>
        </thead>
        <tbody id="tabela-monet"></tbody>
    </table>

    <div class="form-box">
        <h3>Dodawanie rekordu</h3>
        <select id="sel_kraj">
            <?php while($k = $kraje_wynik->fetch_assoc()): ?>
                <option value="<?=$k['id']?>"><?=$k['nazwa']?></option>
            <?php endwhile; ?>
        </select>
        <input type="text" id="inp_nominal" placeholder="Nominał">
        <input type="text" id="inp_nr" placeholder="Nr kat.">
        <select id="sel_metal">
            <?php while($m = $metale_wynik->fetch_assoc()): ?>
                <option value="<?=$m['id']?>"><?=$m['nazwa']?></option>
            <?php endwhile; ?>
        </select>
        <input type="number" id="inp_rok" placeholder="Rok">
        <button onclick="dodaj()">✓</button>
    </div>

    <script>
        function odswiez() {
            fetch('ajax.php?akcja=pobierz')
                .then(r => r.json())
                .then(dane => {
                    let rows = '';
                    dane.forEach(m => {
                        rows += `<tr id="wiersz-${m.id}">
                            <td><img src="flags/${m.symbol_flagi}.png" class="fl" width="30" onclick='edytuj(${JSON.stringify(m)})' title="Kliknij aby edytować"></td>
                            <td>${m.nominal}</td>
                            <td>${m.nr_kat}</td>
                            <td>${m.metal_nazwa}</td>
                            <td>${m.rok}</td>
                            <td><button onclick="usun(${m.id})"> X </button></td>
                        </tr>`;
                    });
                    document.getElementById('tabela-monet').innerHTML = rows;
                });
        }

        function edytuj(m) {
            let tr = document.getElementById(`wiersz-${m.id}`);
            
            let selKraj = document.getElementById('sel_kraj').cloneNode(true);
            selKraj.id = `edit_kraj_${m.id}`;
            selKraj.value = m.id_kraju;

            let selMetal = document.getElementById('sel_metal').cloneNode(true);
            selMetal.id = `edit_metal_${m.id}`;
            selMetal.value = m.id_metalu;

            tr.innerHTML = `
                <td id="td_kraj_${m.id}"></td>
                <td><input type="text" id="edit_nom_${m.id}" value="${m.nominal}" style="width:80px"></td>
                <td><input type="text" id="edit_nr_${m.id}" value="${m.nr_kat}" style="width:80px"></td>
                <td id="td_metal_${m.id}"></td>
                <td><input type="number" id="edit_rok_${m.id}" value="${m.rok}" style="width:60px"></td>
                <td>
                    <button onclick="zapisz(${m.id})">OK</button>
                    <button onclick="odswiez()">Anuluj</button>
                </td>
            `;
            document.getElementById(`td_kraj_${m.id}`).appendChild(selKraj);
            document.getElementById(`td_metal_${m.id}`).appendChild(selMetal);
        }

        function zapisz(id) {
            let p = new URLSearchParams();
            p.append('akcja', 'aktualizuj');
            p.append('id', id);
            p.append('id_kraju', document.getElementById(`edit_kraj_${id}`).value);
            p.append('nominal', document.getElementById(`edit_nom_${id}`).value);
            p.append('nr_kat', document.getElementById(`edit_nr_${id}`).value);
            p.append('id_metalu', document.getElementById(`edit_metal_${id}`).value);
            p.append('rok', document.getElementById(`edit_rok_${id}`).value);

            fetch('ajax.php', { method: 'POST', body: p }).then(() => odswiez());
        }

        function dodaj() {
            let p = new URLSearchParams();
            p.append('akcja', 'dodaj');
            p.append('id_kraju', document.getElementById('sel_kraj').value);
            p.append('nominal', document.getElementById('inp_nominal').value);
            p.append('nr_kat', document.getElementById('inp_nr').value);
            p.append('id_metalu', document.getElementById('sel_metal').value);
            p.append('rok', document.getElementById('inp_rok').value);

            fetch('ajax.php', { method: 'POST', body: p }).then(() => {
                odswiez();
                document.getElementById('inp_nominal').value = '';
                document.getElementById('inp_nr').value = '';
                document.getElementById('inp_rok').value = '';
            });
        }

        function usun(id) {
            if(confirm("Czy na pewno usunąć?")) {
                fetch('ajax.php?akcja=usun&id=' + id).then(() => odswiez());
            }
        }
    </script>
</body>
</html>
