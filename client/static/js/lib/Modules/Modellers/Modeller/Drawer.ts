/// <reference path='./index.ts'/>

/// <reference path='../../../HTMLGenerators/index.ts'/>
/// <reference path='../../../Utils/Datastructures/Point/Point.ts'/>

abstract class Drawer extends ResizingHTMLGenerator<HTMLCanvasElement>
{
    protected CanvasWidth   : number;
    protected CanvasHeight  : number;

    public constructor(width : number, height : number)
    {
        super();
        this.CanvasWidth    = width;
        this.CanvasHeight   = height;
    }

    public abstract Draw();

    protected GenerateElement()
    {
        let canvas = document.createElement("canvas");
        canvas.setAttribute("width", this.CanvasWidth.toString());
        canvas.setAttribute("height", this.CanvasHeight.toString());
        
        return canvas;
    }

    public Resize()
    {
        if(this.Element) {
            let parent = this.Element.parentElement;
            let p = parent.getBoundingClientRect();
            this.CanvasWidth  = p.width;
            this.CanvasHeight = p.height;

            let element = this.Element;
            element.setAttribute("width", this.CanvasWidth.toString());
            element.setAttribute("height", this.CanvasHeight.toString());
        }
    }

    protected ClearCanvas()
    {
        let context = this.Element.getContext("2d");
        context.clearRect(0,0,this.CanvasWidth, this.CanvasHeight);
    }

    public TranslatePointFromViewportToCanvas(point : Point)
    {
        let actualSize = this.Element.getBoundingClientRect();
        let horizontalFrac = this.CanvasWidth / actualSize.width;
        let verticalFrac   = this.CanvasHeight / actualSize.height;

        let newx = Math.round(point.X * horizontalFrac);
        let newy = Math.round(point.Y * verticalFrac);

        let res = new Point(newx, newy);
        return res;
    }

    public TranslatePointFromCanvasToScreen(point : Point)
    {
        let actualSize = this.Element.getBoundingClientRect();
        let horizontalFrac = actualSize.width / this.CanvasWidth;
        let verticalFrac   = actualSize.height / this.CanvasHeight;

        let newx = Math.round(point.X * horizontalFrac);
        let newy = Math.round(point.Y * verticalFrac);

        let res = new Point(newx, newy);
        return res;
    }
    
    public SnapPointToGrid(position : Point)
    {
        let settings = Settings.GetInstance();
        let hs = settings.GetHorizontalSteps();
        let vs = settings.GetVerticalSteps();

        let s = this.CanvasWidth / hs;
        let t = this.CanvasHeight / vs;

        let p = position;
        let h = Math.round(p.X / s);
        let v = Math.round(p.Y / t);

        p.X = h * s;
        p.Y = v * t;

        return p;
    }
    
    protected DrawGrid()
    {
        let settings = Settings.GetInstance();
        let enabled  = settings.GetDisplayGrid();

        if(enabled) {
            let context = this.Element.getContext('2d');
            let hsteps  = settings.GetHorizontalSteps();
            let vsteps  = settings.GetVerticalSteps();

            let sw = this.CanvasWidth / hsteps;
            let sh = this.CanvasHeight / vsteps;

            context.save();
            context.beginPath();
            context.lineWidth = 1;
            context.strokeStyle = "#ccc";

            // vertical lines
            for(let i = 0; i < hsteps; i++) {
                context.moveTo(i * sw, 0);
                context.lineTo(i * sw, this.CanvasHeight);
            }
            // horizontal lines
            for(let i = 0; i < vsteps; i++) {
                context.moveTo(0, i * sh);
                context.lineTo(this.CanvasWidth, i * sh);
            }

            context.closePath();
            context.stroke();
            context.restore();
        }
    }
}