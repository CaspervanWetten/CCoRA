/// <reference path='./index.ts'/>
/// <reference path='../HTMLGenerator.ts'/>
/// <reference path='../HTMLInputGenerator/index.ts'/>

class MenuItem<T> extends HTMLGenerator<HTMLElement>
{
    public Description          : string;
    public Option               : MenuOption<T> | undefined;
    
    public ClassnameDescription : string | undefined;
    public ClassnameOptionBody  : string | undefined;

    protected Binding           : string;

    public constructor(description : string, body?: MenuOption<T>)
    {
        super();
        this.ClassPrefix = "_MENU_ELEMENT__";
        this.Description = description;
        if(body) {
            this.Option = body;
        }
        this.ClassnameDescription = "description";
        this.ClassnameOptionBody  = "option";
    }

    protected GenerateElement()
    {
        let element = document.createElement("li");
        let desc    = document.createElement("div");
        this.AddClassname(desc, this.ClassnameDescription);
        desc.appendChild(document.createTextNode(this.Description));

        element.appendChild(desc);
        if(this.Option) {
            let option  = document.createElement("div");
            this.AddClassname(option, this.ClassnameOptionBody);
            option.appendChild(this.Option.Render());
            element.appendChild(option);
        }
        return element;
    }

    public Update(settings : Settings)
    {
        if(this.Option) {
            let val = settings[this.Binding];
            this.Option.UpdateValue(val);
        }
    }

    //#region Getters and Setters
    public GetDescription()
    {
        return this.Description;
    }

    public SetDescription(desc : string)
    {
        this.Description = desc;
    }

    public GetOption()
    {
        return this.Option;
    }

    public SetOption(body : MenuOption<T>)
    {
        this.Option = body;
    }

    public GetBinding()
    {
        return this.Binding;
    }

    public SetBinding(binding : string)
    {
        this.Binding = binding;
    }
    // #endregion
}