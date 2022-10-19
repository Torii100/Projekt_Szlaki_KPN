const getData = () => {
  // return [{
  //     id: '1',
  //     name: 'name',
  //     latitude: '52.362992',
  //     longitude: '20.383160',
  //     height: '78'
  // }, {
  //     id: '2',
  //     name: 'name',
  //     latitude: '52.363992',
  //     longitude: '20.383160',
  //     height: '78'
  // }]

  return $.ajax({
    url: "db/getData.php",
    async: false,
    type: "GET",
    dataType: "json",
    success: function (result) {
      console.log(result);
      return result;
    },
  });
};

const sendData = (data) => {
  // return {
  //     id: '2',
  //     name: 'nam2',
  //     latitude: '52.362992',
  //     longitude: '20.383160',
  //     height: '790'
  // }

  return $.ajax({
    url: "db/sendData.php",
    type: "POST",
    async: false,
    data: {
      id: data[0] ? data[0] : "-1", // jezeli pole puste podstaw -1
      name: data[1],
      latitude: data[2],
      longitude: data[3],
      height: data[4],
    },
    dataType: "json",
    success: function (res) {
      console.log(res);
      return res;
    },
  });
};

const deleteData = (id) => {
  $.ajax({
    url: "db/deleteData.php",
    async: false,
    type: "POST",
    data: {
      id: id,
    },
    dataType: "json",
  });
};
