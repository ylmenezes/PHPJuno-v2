<?php
    require 'config.php';

    $aResultado = $juno->getCharges($_SERVER['QUERY_STRING']);
?>

<!doctype html>
<html lang="en">
  <head>
    <title>Lista de Boletos</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.24/css/dataTables.bootstrap4.min.css">
  </head>
  <body>
    <div class="container" style="background-color: #d9d9d9;">
        <div class="row">
            <div class="col-12 mr-0 ml-0">
                <fieldset>
                    <legend> Filtro:</legend>

                    <form method="get" class="form m-2 row">

                        <div class="col-4">
                            <div class="form-group m-1">
                                <label>  <input type="radio" name="pesquisa" value="criacao" <?php echo $_GET['pesquisa'] != 'pgto' ? 'checked' : ''; ?> > Por DT. EMISSÃO</label>
                                <hr>
                                <label for="">Dt. Processamento de </label>
                                <input type="date" name="createdOnStart" value="<?php echo $_GET['createdOnStart'] ?: date('Y-m-d'); ?>" class="form-control">
                            </div>

                            <div class="form-group m-1">
                                <label for="">Dt. Processamento até </label>
                                <input type="date" name="createdOnEnd" value="<?php echo $_GET['createdOnEnd'] ?: date('Y-m-d'); ?>" class="form-control">
                            </div>
                        </div>

                        <div class="col-4">
                            <label>  <input type="radio" name="pesquisa" value="pgto" <?php echo $_GET['pesquisa'] == 'pgto' ? 'checked' : ''; ?>> Por DT. PGTO</label>
                            <hr>
                            <div class="form-group m-1">
                                <label for="">Dt. Pgto de </label>
                                <input type="date" name="paymentDateStart" value="<?php echo $_GET['paymentDateStart'] ?: date('Y-m-d'); ?>" class="form-control">
                            </div>

                            <div class="form-group m-1">
                                <label for="">Dt. Pgto até </label>
                                <input type="date" name="paymentDateEnd" value="<?php echo $_GET['paymentDateEnd'] ?: date('Y-m-d'); ?>" class="form-control">
                            </div>
                        </div>
                        
                        <div class="col-4">
                            <div class="form-group m-1">
                                <label></label>
                                <hr>
                                <label for=""> POR PÁGINA: </label>
                                <input type="number" name="pageSize" value="<?php echo $_GET['pageSize'] ?: '100'?>" min="20" max="100" class="form-control">
                            </div>

                            <div class="form-group m-1 mt-2">
                                <label><input type="checkbox" name="pagos" value="true" <?php echo $_GET['pagos'] == true ? 'checked' : '' ;?> > <i class="fa fa-check-circle" aria-hidden="true"></i> Pagos</label>
                            </div>
                        </div>

                        <div class="col-12 text-center">
                            <div class="form-group m-1">
                                <button type="submit" class="btn btn-primary btn-sm"> <i class="fa fa-search" aria-hidden="true"></i> Pesquisar</button>
                            </div>
                        </div>
                    </form>
                </fieldset>
            </div>
        </div>
    </div>

    <div class="container-fluid">
        <div class="row mr-0 ml-0">
            <?php if($aResultado->_embedded): 

                    $aLink = $aResultado->_links;  
                    $aResultado = $aResultado->_embedded->charges; 
            ?>
                
                <div class="col-12 text-center m-1">
                    <a href="<?php echo '?'.parse_url($aLink->previous->href, PHP_URL_QUERY); ?>"> << Anterior</a>
                    <span>Página <?php echo strtoupper($_GET['page']) ?: '1'; ?></span>
                    <a href="<?php echo '?'.parse_url($aLink->next->href, PHP_URL_QUERY); ?>"> Proxima >></a>
                </div>

                <div class="col-12 table-responsive">
                    <table class="table table-striped table-bordered table-hover table-sm">
                        <thead class="thead-dark">

                            <tr>
                                <th class="text-center">#</th>
                                <th class="text-center">Cobrança</th>
                                <th class="text-left">Codigo</th>
                                <th class="text-center">Dt. Vencimento</th>
                                <th class="text-center">Dt. Pgto</th>
                                <th>Valor</th>
                                <th class="text-center">Status Cobrança</th>
                                <th class="text-center">Status Pgto</th>
                                <th></th>
                            </tr>

                            <tbody>
                                <?php  foreach($aResultado as $i => $oResultado): ?>
                                    <tr>
                                        <td class="text-center"><?php echo ++$i; ?></td>
                                        <td class="text-center"><?php echo $oResultado->code; ?></td>
                                        <td class="text-left"><?php 
                                            if( !in_array( $oResultado->payNumber, array('BOLETO PAGO','BOLETO CANCELADO')) ):
                                                echo $oResultado->payNumber;
                                            endif; 
                                        ?></td>
                                        <td class="text-center"><?php echo date('d/m/Y', strtotime($oResultado->dueDate) ); ?></td>
                                        <td class="text-center"><?php 
                                            if($oResultado->payments[0]):
                                                echo date('d/m/Y', strtotime($oResultado->payments[0]->date) );
                                            endif;
                                        ?></td>
                                        <td><?php echo 'R$ '. number_format($oResultado->amount,2,',','.'); ?></td>
                                        <td class="text-center"><?php
                                            
                                            switch($oResultado->status):
                                                case 'PAID':
                                                    echo '<i class="fa fa-check-circle text-success fa-lg" aria-hidden="true" title="Paid"></i>';
                                                    break;

                                                case 'CANCELLED':
                                                    echo '<i class="fa fa-times-circle text-danger fa-lg" aria-hidden="true" title="Cancelado"></i>';
                                                    break;

                                                default:
                                                    echo  '<i class="fa fa-circle-o text-warning fa-lg" aria-hidden="true" title="Aberto"></i>'; 
                                                    break;
                                            endswitch;

                                        ?></td>
                                        <td class="text-center"><?php 
                                            if($oResultado->payments[0]):
                                                switch($oResultado->payments[0]->status):
                                                    case 'CONFIRMED':
                                                        echo '<i class="fa fa-check-circle text-success fa-lg" aria-hidden="true" title="Confirmed"></i>';
                                                        break;

                                                    default:
                                                        echo '';
                                                        break;
                                                endswitch;
                                            endif;
                                        ?></td>
                                        <td class="text-center"><?php 
                                                switch($oResultado->status):
                                                    case 'PAID':
                                                        echo   '<a href="'.$oResultado->link.'" target="_blank" class="btn btn-success btn-sm"> <i class="fa fa-barcode" aria-hidden="true"></i> Ver Boleto </a>';
                                                        break;

                                                    case 'CANCELLED':
                                                        echo   '<a href="'.$oResultado->link.'" target="_blank" class="btn btn-danger btn-sm"> <i class="fa fa-barcode" aria-hidden="true"></i> Ver Boleto </a>';
                                                        break;

                                                    default:
                                                    echo   '<a href="'.$oResultado->link.'" target="_blank" class="btn btn-primary btn-sm"> <i class="fa fa-barcode" aria-hidden="true"></i> Ver Boleto </a>';
                                                        break;
                                                endswitch;
                                        ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </thead>
                    </table>
                </div>
                
            <?php else: ?>
                <div class="col-12"> Sem dados registrados </div>
            <? endif; ?>
        </div>
    </div>
        


    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
    <script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.24/js/dataTables.bootstrap4.min.js"></script>
    <script>
        $('table').DataTable({
            "lengthMenu": [[<?php echo isset($_GET['pageSize']) ? $_GET['pageSize'] : '20'; ?>], [<?php echo isset($_GET['pageSize']) ? $_GET['pageSize'] : '20'; ?>]],
            "oLanguage": {
                "sSearch": "Procurar: ",
                "sLengthMenu": "Mostra _MENU_ registros.",
                "oPaginate": false,
                "sInfo": "(_START_ a _END_) de _TOTAL_",
            },
        });
    </script>
  </body>
</html>