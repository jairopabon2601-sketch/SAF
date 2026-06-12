
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>

<canvas id = "canvas" style="width:100%; max-width:1080px; ">Tu navegador no soporta el elemento canvas!</canvas>


<script>

codigo_calendario = get("codigo_calendario");
fecha_partido="";
lugar_partido="";
 
logos = [
    [
      id="logo_local",
      logo="", 
      x=120,
      y=420
    
    ],
    [
      id="logo_visitante",
      logo="",
      x=740,
      y=420
    ]
]

$.ajax({
    url: "views/torneos/ajax/partido_imprimir_flayer.php",
    type: "GET",
    data: {codigo_calendario: codigo_calendario},
    dataType: "json",
    success: function(data){
      console.log(data);
      logos[0][1] = data[0].escudo_local;
      logos[1][1] = data[0].escudo_visitante;

      fecha_partido = data[0].fecha.replace(/-/g, "/") + " a las " + data[0].hora;
      lugar_partido = data[0].sede;

      generar_canvas();
    }
});


function generar_canvas(){


  const canvas = $("#canvas")[0];
  const ctx = canvas.getContext("2d");
  const mapRoute = "https://www.safenlinea.com/tafi/app/plantillas/proximo_partido.png";
  
  const mapWidth = 1080;
  const mapHeight = 1080;

  canvas.width = mapWidth;
  canvas.height = mapHeight;

  function loadMap(route, name){

    return new Promise((res, rej) =>{
    
      const image = new Image();
      
      image.onload = ()=>{
        res({map: image, name});
      }
      
      image.onerror = e =>{
        rej(e);
      }
      
      image.src = route;
    
    })

  }

  function drawMap(image, ...params){
    ctx.drawImage(image, ...params);
  }


  loadMap(mapRoute, "mapa1").then(mapa =>{
    
    drawMap(mapa.map, 0, 0, mapWidth, mapHeight);

    //imagen del equipo local 
    logo_local = new Image();
    logo_local.src = logos[0][1];
    logo_local.onload = function(){
      ctx.drawImage(logo_local, logos[0][2], logos[0][3], 230, 230);
    }
    

    logo_visitante = new Image();
    logo_visitante.src = logos[1][1];
    logo_visitante.onload = function(){
      ctx.drawImage(logo_visitante, logos[1][2], logos[1][3], 230, 230);
    }
  
    //fecha y hora 
    ctx.font = "bold 26px verdana, sans-serif ";
    ctx.fillStyle = "#FFFFFF";
    ctx.strokeStyle = "#000000";
    ctx.fillText(fecha_partido, (mapWidth / 2) -(ctx.measureText(fecha_partido).width / 2), 800);

    //lugar del partido EN EL CENTRO
    ctx.font = "bold 26px verdana, sans-serif ";
    ctx.fillStyle = "#FFFFFF";
    ctx.strokeStyle = "#000000";
    ctx.fillText(lugar_partido, (mapWidth / 2) -(ctx.measureText(lugar_partido).width / 2), 850);
  
  }).catch(e =>{
    //El mapa no ha podido ser cargado...
    console.error(e);
  });


}

</script>


