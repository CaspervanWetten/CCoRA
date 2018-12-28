/// <reference path='./EventSupervisor/EventSupervisor.ts'/>
/// <reference path='../Utils/Extensions/ChildNode.ts'/>

abstract class HTMLGenerator<T extends HTMLElement>
{
    protected Element           : T | undefined;

    protected ClassPrefix       : string;
    protected ElementId         : string | undefined;
    protected ElementClassname  : string | undefined;

    protected EventSupervisor   : undefined | IEventSupervisor<T, HTMLGenerator<T>>;

    protected abstract GenerateElement() : T

    public constructor()
    {
        this.Element = undefined;
        this.EventSupervisor = undefined;
    }

    protected Generate() : T
    {
        let element = this.GenerateElement();
        if(this.ElementId) element.id = this.ElementId;
        if(this.ElementClassname) this.AddClassname(element, this.ElementClassname);

        return element;
    }

    public Render(force = false) : T
    {
        if(this.Element && !force) return this.Element;
        
        this.Element = this.Generate();
        if(this.EventSupervisor) {
            this.EventSupervisor.Register();
        }
        return this.Element;
    }

    public Remove() : void
    {
        if(this.Element) {
            this.Element.remove();
        }
    }

    protected SetClassname(element : HTMLElement, classname : string) : void
    {
        element.className = "";
        this.AddClassname(element, classname);
    }

    protected AddClassname(element : HTMLElement, classname : string) : void
    {
        let prefixes = this.ClassPrefix.trim().split(' ');
        element.classList.add(...prefixes);
        let classes = classname.trim().split(' ');
        element.classList.add(...classes);
    }

    //#region Getters and setters
    public GetId()
    {
        return this.ElementId;
    }

    public SetId(id : string)
    {
        this.ElementId = id;
    }

    public GetClassname()
    {
        return this.ElementClassname;
    }

    public SetElementClassname(classname : string)
    {
        this.ElementClassname = classname;
        if(this.Element) {
            this.Element.className = "";
            this.AddClassname(this.Element, classname);
        }
    }
    
    public GetElement()
    {
        return this.Element;
    }
    //#endregion
}