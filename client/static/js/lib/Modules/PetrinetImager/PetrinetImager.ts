/// <reference path='./index.ts'/>
/// <reference path='../../Observer/index.ts'/>
/// <reference path='../../ResponseInterpreter/index.ts'/>

/// <reference path='../../Utils/Tools/SVG/Parser.ts'/>

class PetrinetImager implements IResponseInterpreter
{
    protected Container : HTMLElement;
    protected SubInterpreters : IResponseInterpreter[];

    constructor(container : HTMLElement)
    {
        this.Container = container;
        this.SubInterpreters = [];
    }

    public Attach(irp : IResponseInterpreter)
    {
        this.SubInterpreters.push(irp);
    }

    public Detach(irp : IResponseInterpreter)
    {
        let index = this.SubInterpreters.indexOf(irp);
        if(index >= 0) {
            this.SubInterpreters.removeAt(index);
        }
    }

    public ReceiveBusy() {
        this.Container.innerHTML = "";
        this.Container.style.position = "relative;"
        let loader = document.createElement("div");
        loader.classList.add("loader");
        loader.classList.add("absolute_center");
        this.Container.appendChild(loader);

        for(let i = 0; i < this.SubInterpreters.length; i++) {
            this.SubInterpreters[i].ReceiveBusy();
        }
    }
    public ReceiveFailure(code : number, responseText : string) {
        let subs = this.SubInterpreters;
        for(let i = 0; i < subs.length; i++) {
            this.SubInterpreters[i].ReceiveFailure(code, responseText);
        }
    }
    public ReceiveSuccess(code : number, responseText : string) {
        let img : HTMLElement;
        try {
            img = SVGParser.ParseSvg(responseText);
            img.classList.add("petrinetSVG");
            img.style.height        = "100%";
        } catch (e) {
            img = document.createElement("div");
            img.appendChild(document.createTextNode("Could not get an image of the Petri net"));
            img.style.position      = "absolute";
            img.style.top           = "50%";
        }
        img.style.display           = "block";
        img.style.fontFamily        = "sans";
        this.Container.innerHTML    = "";
        this.Container.appendChild(img);
        
        let subs = this.SubInterpreters;
        for(let i = 0; i < subs.length; i++)
        {
            subs[i].ReceiveSuccess(code, responseText);
        }
    }
}
