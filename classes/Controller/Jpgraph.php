<?php
/**
 * User: "Drew J. Sonne" <drews@symbiosislabs.com>
 * Date: 20/10/13
 * Time: 6:03 PM
 * Package: net.symbiosislabs.tech
 */

abstract class Controller_Jpgraph extends Controller
{

    protected $_type = 'line';

    protected $_title = 'Title';

    public function action_index()
    {
        $width = $height = 0;
        foreach (array('height', 'width') as $dimension) {
            $$dimension = $this->request->query($dimension);

            $$dimension = is_null($$dimension) ? 300 : $$dimension;
            $$dimension = $$dimension < 0 ? 0 : $$dimension;
            $$dimension = $$dimension > 1000 ? 1000 : $$dimension;
        }

        $graph = new Graph($width, $height);
        $graph->SetScale('datlin');

        $theme_class = new UniversalTheme;

        $graph->SetTheme($theme_class);
        $graph->img->SetAntiAliasing(false);
        if (!empty($this->_title)) {
//            $graph->title->Set($this->_title);
//            $avg = (sqrt($height*$width)+1)/1001;
//            $graph->title->SetFont(FF_DEFAULT,FS_BOLD,50*$avg);
            $avg = 0;
            $graph->title->SetMargin(40*$avg);
        }
        $graph->SetBox(false);

        $graph->img->SetAntiAliasing();
        $graph->img->SetMargin(35,35,20,0);

        $graph->yaxis->HideZeroLabel();
        $graph->yaxis->HideLine(false);
        $graph->yaxis->HideTicks(false, false);
        $graph->yaxis->SetLabelFormatString("%2.0f%%");

        $graph->xaxis->SetLabelAngle(70);

        $graph->xgrid->Show();
        $graph->xgrid->SetLineStyle("solid");
        $graph->xgrid->SetColor('#E3E3E3');

        foreach ($this->_get_series() as $title => $series) {
            $lineplot = "lp_{$title}";

            // Create the first line
            $$lineplot = new LinePlot($series['data'], $series['date']);
            $$lineplot->setFilled(true);
            $$lineplot->SetFillColor($series['colour'] . "@0.5");

            $graph->Add($$lineplot);

            $$lineplot->SetColor($series['colour']);
            $$lineplot->SetLegend($title);
        }

//        ob_start();
        $graph->Stroke();
//        $image = ob_
        $this->response->headers('Content-Type', 'image/png');
    }

    protected function  _hsv_to_rgb(array $hsv)
    {
        list($H, $S, $V) = $hsv;
        //1
        $H *= 6;
        //2
        $I = floor($H);
        $F = $H - $I;
        //3
        $M = $V * (1 - $S);
        $N = $V * (1 - $S * $F);
        $K = $V * (1 - $S * (1 - $F));
        //4
        switch ($I) {
            case 0:
                list($R, $G, $B) = array($V, $K, $M);
                break;
            case 1:
                list($R, $G, $B) = array($N, $V, $M);
                break;
            case 2:
                list($R, $G, $B) = array($M, $V, $K);
                break;
            case 3:
                list($R, $G, $B) = array($M, $N, $V);
                break;
            case 4:
                list($R, $G, $B) = array($K, $M, $V);
                break;
            case 5:
            case 6: //for when $H=1 is given
                list($R, $G, $B) = array($V, $M, $N);
                break;
        }
        return array($R, $G, $B);
    }

    abstract protected function _get_series();

}