/// <reference path='./index.ts'/>

class Dialog extends HTMLGenerator<HTMLElement>
{
    protected Title : HTMLElement | undefined = undefined;
    protected Body  : HTMLElement | undefined = undefined;

    protected ClassnameTitle : string;
    protected ClassnameBody  : string;

    public constructor()
    {
        super();

        this.ClassPrefix    = "__DIALOG__";
        this.ClassnameTitle = "title";
        this.ClassnameBody  = "body";
    }

    protected GenerateElement()
    {
        let element = document.createElement("div");
        if(this.Title) element.appendChild(this.GetTitle());
        if(this.Body)  element.appendChild(this.GetBody());
        return element;
    }

    //#region Getters and Setters
    public GetTitle()
    {
        return this.Title;   
    }

    public SetTitle(title : string)
    {
        if(this.Title) {
            this.Title.textContent = title;
        } else {
            let element = document.createElement("h1");
            element.appendChild(document.createTextNode(title));
            this.AddClassname(element, this.ClassnameTitle);
            this.Title = element;
        }
    }

    public GetBody()
    {
        return this.Body;
    }
    
    public SetBody(body : HTMLElement)
    {
        if(this.Body) {
            this.Body.innerHTML = body.outerHTML;
        } else {
            let wrapper = document.createElement("div");
            this.AddClassname(wrapper, this.ClassnameBody);
            wrapper.appendChild(body);
            this.Body = wrapper;
        }
    }

    public AppendBody(element : HTMLElement)
    {
        if(this.Body) {
            this.Body.appendChild(element);
        } else {
            this.SetBody(element);
        }
    }

    public GetClassnameTitle()
    {
        return this.ClassnameTitle;
    }

    public SetClassnameTitle(name : string)
    {
        this.ClassnameTitle = name;
        if(this.Title) {
            this.SetClassname(this.Title, name);
        }
    }

    public GetClassnameBody()
    {
        return this.ClassnameBody;
    }

    public SetClassnameBody(name : string)
    {
        this.ClassnameBody = name;
        if(this.Body) {
            this.SetClassname(this.Body, name);
        }
    }
    //#endregion
}