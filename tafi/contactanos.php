
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <meta name="description" content />
        <meta name="author" content />
        <title>TAFI</title>
        <!-- Favicon-->
        <link rel="icon" type="image/x-icon" href="assets/favicon.ico" />
        <!-- Custom Google font-->
        <link rel="preconnect" href="https://fonts.googleapis.com" />
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
        <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@100;200;300;400;500;600;700;800;900&amp;display=swap" rel="stylesheet" />
        <!-- Bootstrap icons-->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css" rel="stylesheet" />
        <!-- Core theme CSS (includes Bootstrap)-->
        <link href="css/styles.css" rel="stylesheet" />
    </head>

    <body class="d-flex flex-column">
        <main class="flex-shrink-0">
            <!-- Navigation-->
            <nav class="navbar navbar-expand-lg navbar-light bg-white py-3">
                <div class="container px-5">
                    <a class="navbar-brand" href="index.html"><span class="fw-bolder text-primary">TAFI</span></a>
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation"><span class="navbar-toggler-icon"></span></button>
                    <div class="collapse navbar-collapse" id="navbarSupportedContent">
                        <ul class="navbar-nav ms-auto mb-2 mb-lg-0 small fw-bolder">
                            <li class="nav-item"><a class="nav-link" href="index.html">Home</a></li>
                            <li class="nav-item"><a class="nav-link" href="#">Torneos Activos</a></li>
                            <li class="nav-item"><a class="nav-link" href="app/index.php">Ingresar</a></li>
                        </ul>
                    </div>
                </div>
            </nav>
            <!-- Page content-->
            <section class="py-5">
                <div class="container px-5">
                    <!-- Contact form-->
                    <div class="bg-light rounded-4 py-5 px-4 px-md-5">
                        <div class="text-center mb-5">
                            <div class="feature bg-primary bg-gradient-primary-to-secondary text-white rounded-3 mb-3"><i class="bi bi-envelope"></i></div>
                            <h1 class="fw-bolder">REGISTRESE </h1>

                        </div>
                        <div class="row gx-5 justify-content-center">
                            <div class="col-lg-8 col-xl-6">

                                <form id="contactForm" name="contactForm" data-sb-form-api-token="API_TOKEN">

                                    <!-- Name input-->
                                    <div class="form-floating mb-3">
                                        <input class="form-control" name="nombre" id="name" type="text" placeholder="Enter your name..."  />
                                        <label for="name">Nombre Completo</label>
                                    </div>

                                    <div class="form-floating mb-3">
                                        <input class="form-control" name="numero_documento" id="name" type="number" placeholder="Enter your name..."  />
                                        <label for="name">N° de Documento</label>
                                    </div>

                                    <div class="form-floating mb-3">
                                        <select class="form-control" name="tipo_documento" id="tipo_documento" type="text" placeholder="Enter your name..." >

                                        </select>   
                                        <label for="name">Tipo de Documento</label>
                                    </div>

                                    
                                    <!-- Email address input-->
                                    <div class="form-floating mb-3">
                                        <input class="form-control" id="email" name="email" type="email" placeholder="name@example.com" data-sb-validations="email" />
                                        <label for="email">Correo</label>
                                        <div class="invalid-feedback" data-sb-feedback="email:email">Email is not valid.</div>
                                    </div>

                                    <!-- Phone number input-->
                                    <div class="form-floating mb-3">
                                        <input class="form-control" id="phone"  name="celular" type="tel" placeholder="(123) 456-7890"  />
                                        <label for="phone">Celular</label>
                                    </div>

                                   <div class="form-floating mb-3">
                                        <input class="form-control" id="name" type="text" name="direccion" placeholder="Enter your name..."  />
                                        <label for="name">Direccion</label>
                                    </div>

                                    <!-- Submit success message-->
                                    <!---->
                                    <!-- This is what your users will see when the form-->
                                    <!-- has successfully submitted-->
                                    <div class="d-none" id="submitSuccessMessage">
                                        <div class="text-center mb-3">
                                            <div class="fw-bolder">Form submission successful!</div>
                                            To activate this form, sign up at
                                            <br />
                                            <a href="https://startbootstrap.com/solution/contact-forms">https://startbootstrap.com/solution/contact-forms</a>
                                        </div>
                                    </div>
                                    <!-- Submit error message-->
                                    <!---->
                                    <!-- This is what your users will see when there is-->
                                    <!-- an error submitting the form-->
                                    <div class="d-none" id="submitErrorMessage"><div class="text-center text-danger mb-3">Error sending message!</div></div>
                                    <!-- Submit Button-->
                                    <div class="d-grid"><button class="btn btn-primary btn-lg" id="btn_enviar" type="button">Enviar Información</button></div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </main>
        <!-- Footer-->
        <footer class="bg-white py-4 mt-auto">
            <div class="container px-5">
                <div class="row align-items-center justify-content-between flex-column flex-sm-row">
                    <div class="col-auto"><div class="small m-0">Copyright © 2023 TAFI</div></div>
                    <div class="col-auto">
                        <a class="small" href="#!">Privacy</a>
                        <span class="mx-1">&middot;</span>
                        <a class="small" href="#!">Terms</a>
                        <span class="mx-1">&middot;</span>
                        <a class="small" href="#!">Contact</a>
                    </div>
                </div>
            </div>
        </footer>


        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
        <script src="https://cdn.startbootstrap.com/sb-forms-latest.js"></script>
        <script
        src="https://code.jquery.com/jquery-3.7.0.js"
        integrity="sha256-JlqSTELeR4TLqP0OG9dxM7yDPqX1ox/HfgiSLBj8+kM="
        crossorigin="anonymous"></script>

        
        <script src="js/funciones_tafi.js"></script>

        <script>
            
            $(document).ready(function () {
                rellenar_select("tbl_tafi_tipo_documento","codigo_tipo_documento","tipo","codigo_tipo_documento","tipo_documento","","tipo");

                $("#btn_enviar").bind("click",grabarRegistro);
            });

            function grabarRegistro(){
                campos=$("#contactForm").serialize();

                console.log(campos);

                $.ajax({
                    url:'ajax/grabar_contacto_web.php',
                    type:'post',
                    dataType:'json',
                    async:false,
                    data: campos,
                    success:function(data,status){
                        if (data.codigo==1){
                            alert(data.mensaje);
                            window.location.href="index.html";
                        }else{
                            alert(data.mensaje);
                        }
                    }
                });
            }

        </script>
    </body>
</html>
