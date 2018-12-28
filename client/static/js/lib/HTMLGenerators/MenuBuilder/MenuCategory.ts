/// <reference path='./index.ts'/>

class MenuCategory extends HTMLGenerator<HTMLElement> implements SettingsObserver
{
    public Name : string;
    protected MenuItems    : StringMap<MenuItem<any>>;

    public ClassnameHeader : string | undefined;
    public ClassnameList   : string | undefined;

    public constructor(name : string)
    {
        super();
        this.ClassPrefix = "__MENU_CATEGORY__";
        this.Name = name;
        this.MenuItems = {};

        this.ClassnameHeader = "header";
        this.ClassnameList   = "list";
    }

    public GenerateElement()
    {
        let container = document.createElement("div");
        let header    = document.createElement("div");

        header.appendChild(document.createTextNode(this.Name));
        if(this.ClassnameHeader) this.AddClassname(header, this.ClassnameHeader);

        container.appendChild(header);

        let menuItemKeys = Object.keys(this.MenuItems);

        if(menuItemKeys.length > 0) {
            let list = document.createElement('ul');
            if(this.ClassnameList) this.AddClassname(list, this.ClassnameList);;
            for(let i = 0; i < menuItemKeys.length; i++)
            {
                list.appendChild(this.MenuItems[menuItemKeys[i]].Render());
            }
            container.appendChild(list)
        }
        return container;
    }

    public AddItem(binding : string, item : MenuItem<any>)
    {
        item.SetBinding(binding);
        this.MenuItems[binding] = item;
    }

    public Update(settings : Settings)
    {
        let keys = Object.keys(settings);
        for(let i = 0; i < keys.length; i++) {
            if(this.MenuItems[keys[i]]) {
                this.MenuItems[keys[i]].Update(settings);
            }
        }
    }

    //#region Getters and Setters

    public GetName()
    {
        return this.Name;
    }

    public SetName(name : string)
    {
        this.Name = name;
    }
    public GetClassnameHeader()
    {
        return this.ClassnameHeader;
    }

    public SetClassnameHeader(name : string)
    {
        this.ClassnameHeader = name;
    }

    public GetClassnameList()
    {
        return this.ClassnameList;
    }

    public SetClassnameList(name : string)
    {
        this.ClassnameList = name;
    }
    //#endregion
}