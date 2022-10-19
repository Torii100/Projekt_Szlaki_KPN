<!doctype html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="style/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">

    <!-- <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css"> -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.0.3/leaflet.css">
    <!-- <link rel="stylesheet" href="geoserwer/css/app.css"> -->
    

    <title>Szlaki - Kampinowski Park Narodowy</title>
</head>
<body>
    
<?php include("nav.php");?>

<main>
<div class="content">
        <h1 class="content-header">Wybrane Szlaki w Kampinowskim Parku Narodowym</h1>

        <!--MAPA -->
        <div id="mymap"></div>

        <!--MODAL DODAWANIE -->
        <button id="addBtn">Dodawanie nowego punktu na szlaku</button>

        <?php
        include('modal.php');
        ?>

        <div class="search-box">
            <label for="search">Wyszukiwarka</label>
            <input id="search" class="search"/>
            <button id="searchBtn" class="search-btn">wyszukaj</button>
        </div>
        <!--Tabela -->
        <div style="width: 100%">
            <table id="list-szlaki">
                <thead>
                <tr>
                    <th>Id</th>
                    <th>Nazwa Punktu</th>
                    <th>Szerokość geo</th>
                    <th>Długość geo</th>
                    <th>Wysokość</th>
                    <th>Operacje</th>
                </tr>
                </thead>
                <tbody id="list">

                </tbody>
            </table>
        </div>

    </div>
</main>

<?php include("footer.php");?>

<script src="https://code.jquery.com/jquery-3.2.0.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.0.3/leaflet.js"></script>
        <script src="geoserwer/js/app.js"></script>

</body>

<script src="db/db_communication.js"></script>
<script>
    $(document).ready(function () {
    //stworzenie wlasnego pomaranczowego znacznika
    const CustomIcon = L.Icon.extend({
            options: {
                iconSize: [40, 40],
            }
        });
        const dotIcon = new CustomIcon({iconUrl: "img/point.png"})

        let markers = {} // json zawierajacy dane znacznikow

        // wylapanie przyciskow
        const modal = $('#myModal')
        const addBtn = $('#addBtn')
        const closeButton = $('#closeModal')
        const submitBtn = $('#submitBtn')
        const modalOperation = $('#modalOperation')

        // wszystkie id z modalu
        const inputs = [
            '#modal-id',
            '#modal-name',
            '#modal-latitude',
            '#modal-longitude',
            '#modal-height',
        ]

        // przypisywanie do przyciskow w tabeli funkcjonalnosci
        const buttonsActions = () => {
            $('button[name=editBtn]').each(function () {
                $(this).on('click', function () {
                    // wstawienie w inputy metadane w przycisku
                    $(inputs[0]).val($(this).data('id'))
                    $(inputs[1]).val($(this).data('name'))
                    $(inputs[2]).val($(this).data('latitude'))
                    $(inputs[3]).val($(this).data('longitude'))
                    $(inputs[4]).val($(this).data('height'))

                    modalOperation.html('Edycja punktu')
                    modal.show()
                })
            })

            $('button[name=deleteBtn').each(function () {
                $(this).on('click', function () {
                    const id = $(this).data('id')
                    deleteData(id)

                    Swal.fire({
                        icon: 'error',
                        title: 'Punkt usunięto pomyślnie',
                        showConfirmButton: false,
                        timer: 1200
                    })

                    map.removeLayer(markers[id])
                    $(this).parents('tr').remove()
                })
            })
        }

        // otwarcie modalu dodawania
        addBtn.on('click', function () {
            modalOperation.html('Dodawanie punktu na szlaku')

            // wyczyszczenie wszystkich pol w modalu
            inputs.forEach(input => {
                $(input).val('')
            })
            modal.show()
        })

        closeButton.on('click', function () {
            modal.hide()
        })

        submitBtn.on('click', function () {
            // pobranie wszystkich wartosci z modalu
            const values = inputs.map(input => {
                return $(input).val()
            })

            // wyslanie danych i otrzymanie utworzonego/zmodyfikowanego obiektu
            const res = sendData(values).responseJSON[0]
            
            // tekst dla otrzymanego elementu
            const thisElement = `
                <tr>
                    <td name='id'>${res.id}</td>
                    <td name='pointName'>${res.name}</td>
                    <td>${res.latitude}</td>
                    <td>${res.longitude}</td>
                    <td>${res.height}</td>
                    <td>
                        <button name="editBtn" class="edit"
                            data-id="${res.id}"
                            data-name="${res.name}"
                            data-latitude="${res.latitude}"
                            data-longitude="${res.longitude}"
                            data-height="${res.height}"
                        >Edytuj</button>
                        <button name="deleteBtn" class="delete"
                            data-id="${res.id}"
                        >Usuń</button>
                    </td>
                </tr>
            `
            
            // dodawanie nowego wiersza
            if (values[0] === '') { // nowy wiersz
                $('#list').append(thisElement)

                markers[res.id] = L.marker([res.latitude, res.longitude], {icon: dotIcon})
                    .bindPopup(`${res.name} - ${res.height} n.p.m.`)
                map.addLayer(markers[res.id])
            } else { // aktualizacja juz istniejacego wiersza
                $('#list').find('td[name=id]').each(function () {
                    if ($(this).text() == values[0]) { // sprawdzenie czy to ten wiersz do podmiany
                        $(this).parents(`tr`).replaceWith(thisElement)

                        map.removeLayer(markers[res.id])
                        markers[res.id] = L.marker([res.latitude, res.longitude], {icon: dotIcon})
                            .bindPopup(`${res.name} - ${res.height} n.p.m.`)
                        map.addLayer(markers[res.id])
                    }
                })
            }

            buttonsActions()
            modal.hide()

            Swal.fire({
                icon: 'success',
                title: 'Zmiany wprowadzono pomyślnie',
                showConfirmButton: false,
                timer: 1200
            })
        })

        // pobranie danych z bazy
        const data = getData().responseJSON
        
        // utworzenie zawartosci tabeli dla wszystkich rekordow
        let tableContent = ""
        data.forEach(point => {
            tableContent += `
                    <tr>
                        <td name='id'>${point.id}</td>
                        <td name='pointName'>${point.name}</td>
                        <td>${point.latitude}</td>
                        <td>${point.longitude}</td>
                        <td>${point.height}</td>
                        <td>
                            <button name="editBtn" class="edit"
                                data-id="${point.id}"
                                data-name="${point.name}"
                                data-latitude="${point.latitude}"
                                data-longitude="${point.longitude}"
                                data-height="${point.height}"
                            >Edytuj</button>
                            <button name="deleteBtn" class="delete"
                                data-id="${point.id}"
                            >Usuń</button>
                        </td>
                    </tr>
                `

            markers[point.id] = L.marker([point.latitude, point.longitude], {icon: dotIcon})
                .bindPopup(`${point.name} - ${point.height} n.p.m.`)
            map.addLayer(markers[point.id])
        })
        $('#list').append(tableContent)
        buttonsActions()

        $('#searchBtn').on('click', function () {
            // pobranie danych z tabeli i z inputa wyszukiwarki
            const searched = $('#search').val().toLowerCase()
            const tableRows = $('#list').find('td[name=pointName]')

            if (!searched) { // pol jest puste
                tableRows.each(function () {
                    $(this).parents('tr').show() // pokazanie wszystkich wierszy
                })
            } else { // pole zawiera tekst
                tableRows.each(function () {
                    if ($(this).text().toLowerCase().includes(searched)) // jezeli komorka zawiera fraze
                        $(this).parents('tr').show()
                    else
                        $(this).parents('tr').hide()
                })
            }

        })


    })
</script>

</html>
