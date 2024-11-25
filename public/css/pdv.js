/* #main{
    font-size: 1.5em;
} */
.shortcut-row {
    height: 200px;
}
.shortcut {
    border: solid 1px #000;
}
.tabla-productos .producto {
    cursor: pointer;
}
.cuenta {
    max-height: 57vh;
    overflow-y: auto;
    overflow-x: hidden;
    height: 100%;
}
.cuenta hr {
    margin-top: 0;
    margin-bottom: 0;
}
.cuenta .total-cuenta {
    position: fixed;
    font-size: 2em;
    bottom: 60px;
    right: 0;
    width: 40%;
    background: #000;
    color: #fff;
    z-index: 999;
}
.cuenta .producto-cantidad {
    cursor: pointer;
}
.cuenta .producto-venta .descuento {
    position: absolute;
    top: 17px;
    right: 10px;
    color: #4dcf6a;
    font-weight: bold;
    font-size: smaller;
}
.cuenta .producto-eliminar {
    margin-top: 8px;
    cursor: pointer;
    color: #f00;
}
.cuenta-comandos {
    height: calc(100vh - 58px);
}
.cuenta-comandos .btn-cobrar {
    position: absolute;
    bottom: 66px;
    left: 0;
    font-weight: bolder;
}
.resultados-container {
    overflow-y: auto;
    height: 80vh;
    max-height: 100%;
}
#resultados tbody tr {
    cursor: pointer;
}
#resultados thead tr:nth-child(1) th {
    background: white;
    position: sticky;
    top: 0;
    z-index: 10;
}
.btn-cobrar, .btn-imprimir-ultima {
    position: fixed;
    right: 0;
    border-radius: 0;
    bottom: 108px;
    width: 40%;
    height: 80px;
    font-size: 2em;
}
.btn-imprimir-ultima{
    bottom: 188px;
}

/*lg*/
@media (max-width: 993px) {
    .cuenta .total-cuenta {
        bottom: 70px;
    }
    #resultados {
        font-size: 0.7em;
    }
}
/*md*/
@media (max-width: 768px) {
    .cuenta .total-cuenta {
        width: 100%;
    }
    #resultados {
        font-size: 1em;
    }
}
/*sm*/
@media (max-width: 576px) {
    .cuenta {
        max-height: 60vh;
    }
}
