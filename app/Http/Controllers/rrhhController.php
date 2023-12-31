<?php

namespace App\Http\Controllers;

use PDF;
use Exception;
use App\Models\rrhh;
use App\Models\Solicitudes;
use App\Models\Empleado;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Carbon\Carbon;
use Jenssegers\Date\Date;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\Style\Alignment;
 




class rrhhController extends Controller
{
    public function index(Request $request)
    {
        $Modelo = new Empleado;
        $direccion = 'rrhh.index';
        $home = 'home';


            $datas = Empleado::orderBy('codigo')->get();

            return view($direccion, compact('datas'));

    }

    public function pdf(Request $request, $id)
    {
        $Modelo = new Empleado;
        $direccion = 'rrhh';
        $home = 'home';
        Date::setLocale('es');

                
            $data = Empleado::findOrFail($id);
            $data1 = Solicitudes::where('empleado', '=', $id)->where('estado','=','ACTIVO')->get();
           
            $contador=count($data1);
            $contadorfecha= count($data1)-1;    
        
            
            $fechafincontrato=$data1[$contadorfecha]->fin;    
        


            $count=1;
            $count++;
           
            
            
            
          $fechaActual = Carbon::now();
          $fechaformateada = Date::now()->format('Y-m-d');

        

           

          if(strtotime($fechafincontrato) < strtotime($fechaformateada)){
          
                  $fechaActual = Date::now()->format('d \d\e F  Y');

                  $pdf = app('dompdf.wrapper');
                  $pdf->getDomPDF()->set_option("enable_php", true);
                  $pdf->loadView('rrhh.pdffecha', compact('data','data1','fechaActual','contador','count'))->setPaper('letter');
                
                  return $pdf->download('Constancia '.$data->nombres.' '.$data->apellidos.'.pdf'); }

            else{

           
            
              $fechaActual = Date::now()->format('d \d\e F  Y');

              $pdf = app('dompdf.wrapper');
              $pdf->getDomPDF()->set_option("enable_php", true);
              $pdf->loadView('rrhh.pdf', compact('data','data1','fechaActual','contador','count'))->setPaper('letter');
            
              return $pdf->download('Constancia '.$data->nombres.' '.$data->apellidos.'.pdf'); }
            
            
            


            
            



         //return view('rrhh.pdf', compact('datas'));

    }


    public function word(Request $request, $id)
    {
        $Modelo = new Empleado;
        $direccion = 'rrhh';
        $home = 'home';
        Date::setLocale('es');

                
            $data = Empleado::findOrFail($id);
            $data1 = Solicitudes::where('empleado', '=', $id)->where('estado','=','ACTIVO')->get();
            $phpWord = new PhpWord();

            
            $fechaActual = Carbon::now();
            $fechaActual = Date::now()->format('d \d\e F  Y');
            $section = $phpWord->addSection();
            
           
            
            // Agregar texto centrado al documento
           //  $section->addText('LA INFRASCRITA JEFA DE LA SECCIÓN DE PERSONAL DEL INSTITUTO NACIONAL DE ESTADÍSTICA',array('bold' => true , 'align' => 'center' ));
                    
             $section->addText(
                htmlspecialchars(
                  'LA INFRASCRITA JEFA DE LA SECCIÓN DE PERSONAL DEL INSTITUTO NACIONAL DE ESTADÍSTICA'
                ),
                array('name' => 'Arial', 'size' => '12', 'bold' => 'true','alignment' => 'center')
              );


              $section->addText(
                htmlspecialchars(
                  'HACE CONSTAR'
                ),
                array('name' => 'Arial', 'size' => '12', 'bold' => 'true','alignment' => 'center')
              );

              
              $section->addText(
                htmlspecialchars(
                  "Que $data->nombres $data->apellidos,suscribió contrato por servicios $data->tiposervicio en la institución, en las siguientes fechas: "
                ),
                array('name' => 'Arial', 'size' => '12', 'bold' => 'true','alignment' => 'center')
              );






              $estilo_tabla = array(
                'borderColor' => 'F2F2F2',
                'borderSize' => '5',
                'cellMargin' => '20',
                'bgColor' => '088A68',
            );
            
            $primera_fila = array('bgColor' => 'F2F2F2');
            $section->addTableStyle('mitabla',$estilo_tabla, $primera_fila);
            $tabla = $section->addTable('mitabla');
            
            

                    $filePath = 'constancia '.$data->nombres.' '.$data->apellidos.'.docx';
                    $objWriter = IOFactory::createWriter($phpWord, 'Word2007');
                    $objWriter->save($filePath);
                    return response()->download($filePath)->deleteFileAfterSend();
           
            


         //return view('rrhh.pdf', compact('datas'));

    }

    public function pdf2($id)
    {

        $Modelo= new rrhh;
        $direccion='rrhh';
        //$home = 'home';


        //$datas = rrhh::paginate ( "select * from rrhh where $id = $id");

        $datas = rrhh::where('soli_id',$id)->get();


        $pdf = PDF::loadView('rrhh.pdf2', ['datas'=>$datas]);
        return $pdf->stream();


         //return view('rrhh.pdf', compact('datas'));

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {

        $Modelo = new Solicitudes();
        $direccion = 'rrhh.crear';
            return view($direccion);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {



        $Modelo = new Solicitudes();
        $direccion = 'rrhh';

     //dd($request);

        Solicitudes::create($request->all());


            return redirect('rrhh')->with('mensaje', 'rrhh creada exitosamente.');

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
      // dd($request);
        $Modelo=new Empleado;
        $direccion='rrhh.mostrar';

            $data = Empleado::findOrFail($id);
            $data1 = Solicitudes::where('empleado', '=', $id)->where('estado','=','ACTIVO')->get();
            return view('rrhh.mostrar', compact('data', 'data1'));
    }



    
    public function buscar(Request $request)
    {
        $query = $request->input('q');
        $posts = Empleado::search($query)->get();

        
        return view('rrhh.index', compact('datas','posts'));

        
    }





    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id)
    {
        $Modelo = new Solicitudes();
        $direccion = 'rrhh.editar';

        $data = Solicitudes::findOrFail($id);
        return view($direccion, compact('data'));


    }



    public function edit1(Request $request, $id)
    {
        $Modelo = new Solicitudes();
        $direccion = 'rrhh.editar1';

        $data = Solicitudes::findOrFail($id);
        return view($direccion, compact('data'));


    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {

        $Modelo = new Solicitudes();
        $direccion = 'rrhh';


        Solicitudes::findOrFail($id)->update($request->all());
        return redirect($direccion)->with('mensaje', 'rrhh actualizado correctamente.');




    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        $Modelo = new rrhh();
        $direccion = 'rrhh';


            try {
                rrhh::destroy($id);
            } catch (Exception $e) {


                return redirect($direccion)->withErrors(['catch', $e->getMessage()]);
            }


            return redirect($direccion)->with('mensaje', 'rrhh eliminanda correctamente.');
        }








}
