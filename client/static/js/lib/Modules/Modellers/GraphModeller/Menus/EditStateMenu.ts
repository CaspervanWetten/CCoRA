// /// <reference path='./index.ts'/>

// /// <reference path='../../../../Models/index.ts'/>
// /// <reference path='../../../../Modules/Modellers/index.ts'/>
// /// <reference path='../../../../Drawings/index.ts'/>
// /// <reference path='../../../../HTMLGenerators/index.ts'/>

/// <reference path='./index.ts'/>
/// <reference path='../index.ts'/>

class EditStateMenu extends GraphModellerMenu
{
    public Inputs   : HTMLInputElement[];
    public Drawing  : StateDrawing;
    public Popup    : Popup;

    // private Size    : Size;

    public constructor(modeller : GraphModeller, sd : StateDrawing)
    {
        super(modeller);
        this.Drawing = sd;
        this.Inputs = [];
    }

    public Focus()
    {
        if(this.Element == null) return;
        this.Inputs[0].focus();
        this.Inputs[0].select();
    }

    public SetLeft(left ?: number)
    {
        let drawing = this.Drawing;        
        let context = this.Modeller.GetDrawer().GetElement().getContext("2d");
        let state   = drawing.GetState();
        let pos     = drawing.GetPosition();
        let size    = drawing.GetSize(context);
        let container = this.Modeller.GetDrawer().GetElement();

        if(left != null) {
            this.Popup.SetLeft(left);
            return;
        }
        let p = this.Popup;
        let rect = p.GetBody().getBoundingClientRect();
        let crect = container.getBoundingClientRect();
        left = Math.max(pos.X + size.Width / 2, rect.width / 2);
        left = Math.min(left, crect.width - rect.width / 2);
        p.SetLeft(left);
    }

    public SetTop(top?: number)
    {
        let drawing = this.Drawing;        
        let context = this.Modeller.GetDrawer().GetElement().getContext("2d");
        let state   = drawing.GetState();
        let pos     = drawing.GetPosition();
        let size    = drawing.GetSize(context);
        let sh      = Settings.GetInstance().GetStateHeight();
        let container = this.Modeller.GetDrawer().GetElement();        

        if(top != null) {
            this.Popup.SetTop(top);
            return;
        }
        let p = this.Popup;
        let rect = this.Popup.GetBody().getBoundingClientRect();
        let crect = container.getBoundingClientRect();
        top = pos.Y + sh + 5;
        if(rect.height + top > crect.height) {
            top = pos.Y - rect.height - 15;
        }
        p.SetTop(top);
        // p.SetTop(pos.Y + sh + 5);
    }

    protected Confirm()
    {
        let store   = Store.GetInstance();
        let net     = store.GetPetrinet();
        let places  = net.GetPlaces().sort();

        let sd = this.Drawing;
        let state = sd.GetState();

        let s = "";
        let k = "";
        for(let i = 0; i < this.Inputs.length - 1; i++) {
            k = this.ParsePlaceValue(this.Inputs[i].value);
            s += places[i] + ":" + k + ",";
        }
        k = this.ParsePlaceValue(this.Inputs[this.Inputs.length - 1].value);
        s += places[this.Inputs.length - 1] + ":" + k;
        
        let a = new EditState(this.Modeller.GetDrawer(), this.Drawing.GetState(), s);
        this.Modeller.ExecuteAndStore(a);

        this.Modeller.Drawer.Draw();
    }

    protected ParsePlaceValue(s : string)
    {
        let k = s;
        k.trim();
        if(k == "") {
            k = "0";
        }
        return k;
    }

    protected GetElement()
    {
        let p = new Popup();

        let context = this.Modeller.GetDrawer().GetElement().getContext("2d");
        let drawing = this.Drawing;
        let state   = drawing.GetState();
        let store   = Store.GetInstance();
        let net     = store.GetPetrinet();
        let places  = net.GetPlaces().sort();

        let inputs  = this.Inputs;

        let placesContainer = document.createElement("div");
        placesContainer.classList.add("places");
        for(let i = 0; i < places.length; i++)  {
            // container
            let e = document.createElement("div");
            e.classList.add("place");
            // place text
            let place = document.createElement("div");
            place.appendChild(document.createTextNode(places[i]));
            place.classList.add("name");
            // input
            let inp = document.createElement("input");
            inp.classList.add("input");
            let val = state.GetPlace(places[i]);
            if (val == null) val = new IntToken(0);
            inp.setAttribute("type", "text");
            inp.setAttribute("value", val.ToString());

            inp.addEventListener("keydown", (e)=> {
                if(e.keyCode == 9 || e.which == 9) {        // tab
                    e.preventDefault();
                    if(!e.shiftKey) {
                        let j = i + 1;
                        if(j >= inputs.length) j = 0;
                        inputs[j].focus();
                        inputs[j].select();
                    }
                    else {
                        let j = i - 1;
                        if(j < 0) j = inputs.length - 1;
                        inputs[j].focus();
                        inputs[j].select();
                    }
                }

                if(e.keyCode == 13 || e.which == 13) {      // enter
                    this.Confirm();
                    this.Hide();
                }
            });

            inputs[i] = inp;

            let buttonrow = document.createElement("div");
            buttonrow.classList.add("buttons");

            let minusButton = document.createElement("button");
            minusButton.appendChild(document.createTextNode("-"));
            minusButton.addEventListener("click", (e)=>{
                let current = parseInt(inp.value);
                if(isNaN(current)) {
                    inp.value = "0";
                }
                else {
                    let s =  Math.max(Number(inp.value) - 1, 0);
                    inp.value = s.toString();
                }
            });

            let omegaButton = document.createElement("button");
            omegaButton.appendChild(document.createTextNode("ω"));
            omegaButton.addEventListener("click", (e)=>{
                inp.value = "ω";
            });

            let plusButton = document.createElement("button");
            plusButton.appendChild(document.createTextNode("+"));
            plusButton.addEventListener("click", (e)=> {
                let current = parseInt(inp.value);
                if(isNaN(current)) {
                    inp.value = "1";
                }
                else {
                    let s = Math.max(Number(inp.value) + 1, 0);
                    inp.value = s.toString();
                }
            });

            buttonrow.appendChild(minusButton);
            buttonrow.appendChild(omegaButton);
            buttonrow.appendChild(plusButton);

            e.appendChild(place);
            e.appendChild(inp)
            e.appendChild(buttonrow);
            placesContainer.appendChild(e);
        }
        
        let buttonContainer = document.createElement("div");
        buttonContainer.classList.add("buttons");

        let confirmButton = document.createElement("button");
        confirmButton.appendChild(document.createTextNode("Change"));
        confirmButton.classList.add("confirm");
        confirmButton.addEventListener("click", (e)=>{
            this.Confirm();
            this.Hide();
        });

        let cancelButton  = document.createElement("button");
        cancelButton.appendChild(document.createTextNode("Cancel"));
        cancelButton.classList.add("cancel");
        cancelButton.addEventListener("click", (e)=>{
            this.Hide();
        });

        buttonContainer.appendChild(cancelButton);
        buttonContainer.appendChild(confirmButton);
        buttonContainer.classList.add("buttons");
        
        p.SetBody(placesContainer)
        p.AppendBody(buttonContainer);
        
        p.SetElementClassname("edit state");

        this.Popup = p;
        let e = p.Render();
        return e;
    }

    protected GetParent()
    {
        return this.Modeller.GetDrawer().GetElement().parentElement;
    }
}