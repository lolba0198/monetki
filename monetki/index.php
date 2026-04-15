<?php
$db = new mysqli("localhost", "root", "ServBay.dev", "monetki");
$db->set_charset("utf8");

$kraje_wynik = $db->query("SELECT * FROM kraje");
$metale_wynik = $db->query("SELECT * FROM metale");
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
        .fl { border: 1px solid black; }
    </style>
</head>
<body onload="odswiez()">

    <table>
        <thead>
            <tr>
                <th>Flaga</th><th>Nominał</th><th>Nr Kat.</th><th>Stop</th><th>Rok</th><th>Usuń</th>
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
                        rows += `<tr>
                            <td><img src="flags/${m.symbol_flagi}.png" class="fl" width="30"></td>
                            <td>${m.nominal}</td>
                            <td>${m.nr_kat}</td>
                            <td>${m.metal_nazwa}</td>
                            <td>${m.rok}</td>
                            <td><button onclick="usun(${m.id})">X</button></td>
                        </tr>`;
                    });
                    document.getElementById('tabela-monet').innerHTML = rows;
                });
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
            });
        }

        function usun(id) {
            fetch('ajax.php?akcja=usun&id=' + id).then(() => odswiez());
        }
    </script>
</body>
</html>