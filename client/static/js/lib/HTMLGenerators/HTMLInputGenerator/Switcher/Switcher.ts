/// <reference path='./index.ts'/>
/// <reference path='../index.ts'/>

enum SwitchState {
    Off = 0,
    On
}

class Switcher extends HTMLInputGenerator<HTMLElement, SwitchState>
{
    public ToOnFunction     : () => any;
    public ToOffFunction    : () => any;

    public ClassnameOn      : string;
    public ClassnameOff     : string;
    public ClassnameLabel   : string | undefined;
    public OnLabel          : string | undefined;
    public OffLabel         : string | undefined;

    public constructor(
        toOn : () => any, 
        toOff : () => any, 
        start = SwitchState.Off)
    {
        super();
        this.ClassPrefix = "__SWITCHER__";

        this.ToOnFunction = toOn;
        this.ToOffFunction = toOff;

        this.ClassnameOn    = "on";
        this.ClassnameOff   = "off";
        this.ClassnameLabel = "label";

        this.OnLabel = "on";
        this.OffLabel = "off";

        this.Value = start;
    }

    public UpdateValue(newValue : SwitchState) {
        if(this.Value != newValue) {
            this.Toggle(true);
        }
    }

    public Toggle(external : boolean = false)
    {
        if(this.Value == SwitchState.Off)
        {
            if(!external) {
                this.ToOnFunction();
            }
            this.Value = SwitchState.On;
            this.SetElementClassname("on");
        } else
        {
            if(!external) {
                this.ToOffFunction();
            }
            this.Value = SwitchState.Off;
            this.SetElementClassname("off");
        }
    }

    public GenerateElement()
    {
        let element = document.createElement("div");
        if(this.Value == SwitchState.Off) {
            this.AddClassname(element, this.ClassnameOff);
        } else {
            this.AddClassname(element, this.ClassnameOn);
        }

        // left label
        let l = document.createElement("div");
        this.AddClassname(l, this.ClassnameLabel);
        this.AddClassname(l, this.ClassnameOn);
        l.appendChild(document.createTextNode(this.OffLabel));

        // right label
        let r = document.createElement("div");
        this.AddClassname(r, this.ClassnameLabel);
        this.AddClassname(r, this.ClassnameOff);
        r.appendChild(document.createTextNode(this.OnLabel));
        
        // actual switch
        let s = document.createElement("div");
        this.AddClassname(s, "switch");

        element.appendChild(l);
        element.appendChild(s);
        element.appendChild(r);

        s.addEventListener("click", ()=>{
            this.Toggle();
        });

        return element;
    }
}