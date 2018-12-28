/// <reference path='./index.ts'/>
/// <reference path='../../Utils/Datastructures/Queue/Queue.ts'/>

class FormBuilder extends HTMLGenerator<HTMLFormElement>
{
    protected FormElements  : HTMLElement[];

    protected Action        : string | undefined;
    protected Method        : string | undefined;
    protected Enctype       : string;

    protected SubmitOnEnter : boolean;
    
    public constructor(submitOnEnter)
    {
        super();
        this.FormElements = [];
        this.Enctype  = "multipart/form-data";
        this.ClassPrefix = "__FORM__";

        this.SubmitOnEnter = submitOnEnter;
    }

    protected GenerateElement()
    {
        let form = document.createElement("form");
        form.enctype = this.Enctype;
        if(this.Method) form.method  = this.Method;
        if(this.Action) form.action  = this.Action;

        for(let i = 0; i < this.FormElements.length; i++) {
            form.appendChild(this.FormElements[i]);
        }

        return form;
    }

    protected Generate()
    {
        let element = super.Generate();
        if(!this.SubmitOnEnter) {
            this.PreventSubmit(element);
        }
        return element;
    }

    protected PreventSubmit(element : HTMLElement)
    {
        element.addEventListener("submit", (e)=>{
            e.preventDefault();
        });
    }

    protected AddElement(element : HTMLElement)
    {
        this.FormElements.push(element);
    }

    public AddInput(name : string, type : string, placeholder? : string)
    {
        let input = document.createElement("input");
        input.setAttribute("name", name);
        input.setAttribute("type", type);
        if(placeholder) input.setAttribute("placeholder", placeholder);

        this.AddElement(input);
        return input;
    }

    public AddTextArea(name : string, placeholder? : string)
    {
        let area = document.createElement("textarea");
        area.setAttribute("name", name);
        if(placeholder) area.setAttribute("placeholder", placeholder);

        this.AddElement(area);
        return area;
    }

    public AddSelect(name : string, values : string[])
    {
        let select = document.createElement("select");
        for(let i = 0; i < values.length; i++) {
            let value = values[i];
            let option = document.createElement("option");
            option.setAttribute("value", value);
            option.appendChild(document.createTextNode(value));

            select.appendChild(option);
        }

        select.setAttribute("name", name);
        this.AddElement(select);
    }

    public AddLabel(text : string, _for? : string)
    {
        let label = document.createElement("label");
        label.appendChild(document.createTextNode(text));
        if(_for) label.setAttribute("for", _for);

        this.AddElement(label);
        return label;
    }

    public AddHTML(element : HTMLElement)
    {
        this.AddElement(element);
    }

    //#region Getters and Setters
    public GetAction() 
    {
        return this.Action;
    }

    public SetAction(action : string)
    {
        this.Action = action;
    }

    public GetMethod()
    {
        return this.Method;
    }

    public SetMethod(method : string)
    {
        this.Method = method;
    }

    public GetEnctype()
    {
        return this.Enctype;
    }

    public SetEnctype(enctype : string)
    {
        this.Enctype = enctype;
    }

    public GetSubmitOnEnter()
    {
        return this.SubmitOnEnter;
    }

    public SetSubmitOnEnter(submit : boolean)
    {
        this.SubmitOnEnter = submit;
    }
    //#endregion
}