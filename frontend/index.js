const baseURL = "http://127.0.0.1:8080";

var listas = [];
var utilizador = {};

document.getElementById("submit-login").onclick = function () {
  var url = new URL(baseURL + "/user/login");

  let dados = {};

  dados.username = document.getElementById("login-username").value;
  dados.password = document.getElementById("login-password").value;

  fetch(url, {
    method: "POST",
    mode: "cors",
    headers: {
      Accept: "*/*",
      "Content-Type": "application/json",
    },
    body: JSON.stringify(dados),
  })
    .then((res) => res.json())
    .then(function (data) {
      if (data.err) {
        alert(data.err);
      } else {
        localStorage.setItem("token", data.token);

        utilizador = data;

        document.getElementById("login").style.display = "none";
        document.getElementById("listas").style.display = "block";
        document.getElementById("logout").style.display = "unset";
        document.getElementById("botao-editar").style.display = "unset";

        carregarListas();

        document.getElementById("login-username").value = "";
        document.getElementById("login-password").value = "";
      }
    })
    .catch((error) => console.log(error));
};

document.getElementById("logout").onclick = function () {
  var url = new URL(baseURL + "/user/logout");

  fetch(url, {
    method: "POST",
    mode: "cors",
    headers: {
      Accept: "*/*",
      "Content-Type": "application/json",
      Authorization: localStorage.getItem("token"),
    },
  })
    .then(function (res) {
      localStorage.removeItem("token");

      document.getElementById("login").style.display = "block";
      document.getElementById("botao-editar").style.display = "none";
      document.getElementById("listas").style.display = "none";
      document.getElementById("logout").style.display = "none";
      document.getElementById("editar").style.display = "none";
      document.getElementById("itens").style.display = "none";
    })
    .catch((error) => console.log(error));
};

document.getElementById("botao-editar").onclick = function () {
  document.getElementById("editar").style.display = "unset";
  document.getElementById("logout").style.display = "unset";
  document.getElementById("login").style.display = "none";
  document.getElementById("listas").style.display = "none";
  document.getElementById("itens").style.display = "none";

  document.getElementById("editar-nome").value = utilizador.nome;
  document.getElementById("editar-email").value = utilizador.email;
  document.getElementById("editar-username").value = utilizador.username;
  document.getElementById("registo-password").value = "";
};

document.getElementById("submit-editar").onclick = function () {
  var url = new URL(baseURL + "/user");

  let dados = {};

  dados.nome = document.getElementById("editar-nome").value;
  dados.email = document.getElementById("editar-email").value;
  dados.username = document.getElementById("editar-username").value;
  dados.password = document.getElementById("editar-password").value;

  fetch(url, {
    method: "PUT",
    mode: "cors",
    headers: {
      Accept: "*/*",
      "Content-Type": "application/json",
      Authorization: localStorage.getItem("token"),
    },
    body: JSON.stringify(dados),
  })
    .then(function (res) {
      carregarListas();
      utilizador.nome = dados.nome;
      utilizador.email = dados.email;
      utilizador.username = dados.username;
    })
    .catch((error) => console.log(error));
};

document.getElementById("botao-registo").onclick = function () {
  document.getElementById("login").style.display = "none";
  document.getElementById("listas").style.display = "none";
  document.getElementById("logout").style.display = "none";

  document.getElementById("registo").style.display = "block";
};

document.getElementById("botao-login").onclick = function () {
  document.getElementById("registo").style.display = "none";
  document.getElementById("listas").style.display = "none";
  document.getElementById("logout").style.display = "none";

  document.getElementById("login").style.display = "block";
};

document.getElementById("submit-registo").onclick = function () {
  var url = new URL(baseURL + "/user/registo");

  let dados = {};

  dados.nome = document.getElementById("registo-nome").value;
  dados.email = document.getElementById("registo-email").value;
  dados.username = document.getElementById("registo-username").value;
  dados.password = document.getElementById("registo-password").value;

  fetch(url, {
    method: "POST",
    mode: "cors",
    headers: {
      Accept: "*/*",
      "Content-Type": "application/json",
    },
    body: JSON.stringify(dados),
  })
    .then(function (res) {
      document.getElementById("login").style.display = "block";
      document.getElementById("registo").style.display = "none";
      document.getElementById("listas").style.display = "none";
      document.getElementById("logout").style.display = "none";

      document.getElementById("registo-nome").value = "";
      document.getElementById("registo-email").value = "";
      document.getElementById("registo-username").value = "";
      document.getElementById("registo-password").value = "";
    })
    .catch((error) => console.log(error));
};

document.getElementById("submeter-lista").onclick = function () {
  var url = new URL(baseURL + "/listas");

  let dados = {};

  dados.nome = document.getElementById("lista-nome").value;

  fetch(url, {
    method: "POST",
    mode: "cors",
    headers: {
      Accept: "*/*",
      "Content-Type": "application/json",
      Authorization: localStorage.getItem("token"),
    },
    body: JSON.stringify(dados),
  })
    .then(function (data) {
      carregarListas();
      document.getElementById("lista-nome").value = "";
    })
    .catch((error) => console.log(error));
};

function carregarListas() {
  var url = new URL(baseURL + "/listas");

  document.getElementById("listas").style.display = "block";
  document.getElementById("itens").style.display = "none";
  document.getElementById("editar").style.display = "none";

  fetch(url, {
    method: "GET",
    mode: "cors",
    headers: { Accept: "*/*", Authorization: localStorage.getItem("token") },
  })
    .then((res) => res.json())
    .then(function (data) {
      let antigas = [];
      let ativas = [];
      listas = data;

      for (let i = 0; i < data.length; i++) {
        if (data[i].fechada) {
          antigas.push(data[i]);
        } else {
          ativas.push(data[i]);
        }
      }

      let elementoAntigas = document.getElementById("listas-antigas");
      let elementoAtivas = document.getElementById("listas-ativas");
      elementoAntigas.innerHTML = "";
      elementoAtivas.innerHTML = "";

      for (let i = 0; i < antigas.length; i++) {
        elementoAntigas.innerHTML +=
          '<li class="list-group-item d-flex justify-content-between"><span>' +
          antigas[i].nome +
          '</span><button class="btn btn-primary" style="margin-right: 20px;" onclick="verItens(' +
          antigas[i].id +
          ')">Ver Itens</button></li>';
      }

      for (let i = 0; i < ativas.length; i++) {
        elementoAtivas.innerHTML +=
          '<li class="list-group-item d-flex justify-content-between"><span>' +
          ativas[i].nome +
          '</span><div"><button class="btn btn-primary" style="margin-right: 20px;"  onclick="verItens(' +
          ativas[i].id +
          ')">Ver Itens</button><button class=" btn btn-success" onClick="completarLista(' +
          ativas[i].id +
          ')">Completar Lista</button></div></li>';
      }
    })
    .catch((error) => console.log(error));
}

function verItens(idlista) {
  var url = new URL(baseURL + "/listas/" + idlista + "/itens");

  document.getElementById("listas").style.display = "none";
  document.getElementById("itens").style.display = "block";

  fetch(url, {
    method: "GET",
    mode: "cors",
    headers: {
      Accept: "*/*",
      "Content-Type": "application/json",
      Authorization: localStorage.getItem("token"),
    },
  })
    .then((res) => res.json())
    .then(function (data) {
      if (data.err) {
        alert(data.err);
      } else {
        let lista = {};

        for (let i = 0; i < listas.length; i++) {
          if (listas[i].id == idlista) {
            lista = listas[i];
          }
        }

        document.getElementById("titulo-lista").innerText =
          "Itens da Lista: " + lista.nome;

        if (lista.fechada) {
          document.getElementById("adicionar-item").style.display = "none";
        } else {
          document.getElementById("adicionar-item").style.display = "unset";
        }

        let elemento_itens = document.getElementById("lista-itens");
        elemento_itens.innerHTML = "";

        for (let i = 0; i < data.length; i++) {
          let item = data[i];

          let elementos =
            '<div class="card me-4 mb-4" style="width: 18rem"><div class="card-body"><h5 class="card-title">' +
            item.nome +
            '</h5><h6 class="card-subtitle mb-2 text-muted">Quantidade: ' +
            item.quantidade +
            '</h6><h6 class="card-subtitle mb-2 text-muted">Comprado: ' +
            (item.comprado ? "Sim" : "Não") +
            '</h6><p class="card-text">' +
            item.observacoes +
            "</p>";

          if (!lista.fechada) {
            elementos +=
              '<button class="btn btn-danger" onClick="removerItem(' +
              idlista +
              ", " +
              item.id +
              ');">Remover Item</button>';

            if (!item.comprado) {
              elementos +=
                '<button class="btn btn-warning ms-2" onClick="comprarItem(' +
                idlista +
                ", " +
                item.id +
                ');">Comprado</button>';
            }
          }

          elementos += "</div></div>";
          elemento_itens.innerHTML += elementos;
        }
      }
    })
    .catch((error) => console.log(error));

  document.getElementById("submeter-item").onclick = function () {
    var url = new URL(baseURL + "/itens");

    let dados = {};

    dados.nome = document.getElementById("item-nome").value;
    dados.quantidade = document.getElementById("item-quantidade").value;
    dados.observacoes = document.getElementById("item-observacoes").value;
    dados.lista = idlista;

    fetch(url, {
      method: "POST",
      mode: "cors",
      headers: {
        Accept: "*/*",
        "Content-Type": "application/json",
        Authorization: localStorage.getItem("token"),
      },
      body: JSON.stringify(dados),
    })
      .then(function (data) {
        verItens(idlista);
        document.getElementById("item-nome").value = "";
        document.getElementById("item-quantidade").value = "";
        document.getElementById("item-observacoes").value = "";
      })
      .catch((error) => console.log(error));
  };
}

function removerItem(idlista, iditem) {
  var url = new URL(baseURL + "/itens/" + iditem);

  fetch(url, {
    method: "DELETE",
    mode: "cors",
    headers: {
      Accept: "*/*",
      "Content-Type": "application/json",
      Authorization: localStorage.getItem("token"),
    },
  })
    .then(function (data) {
      verItens(idlista);
    })
    .catch((error) => console.log(error));
}

function comprarItem(idlista, iditem) {
  var url = new URL(baseURL + "/itens/" + iditem + "/comprado");

  fetch(url, {
    method: "POST",
    mode: "cors",
    headers: {
      Accept: "*/*",
      "Content-Type": "application/json",
      Authorization: localStorage.getItem("token"),
    },
  })
    .then(function (data) {
      verItens(idlista);
    })
    .catch((error) => console.log(error));
}

function completarLista(idlista) {
  var url = new URL(baseURL + "/listas/" + idlista + "/fechar");

  fetch(url, {
    method: "POST",
    mode: "cors",
    headers: {
      Accept: "*/*",
      "Content-Type": "application/json",
      Authorization: localStorage.getItem("token"),
    },
  })
    .then(function (data) {
      carregarListas(idlista);
    })
    .catch((error) => console.log(error));
}

function editarPerfil() { }
