/// <reference path='./index.ts'/>

class ContextMenu extends Popup
{
    protected MenuItems         : ContextMenuItem[];
    protected ItemClassname     : string | undefined;

    public constructor(left=0, top=0)
    {
        super();
        this.ClassPrefix = "__CONTEXT_MENU__";
        this.Closeable = true;

        this.MenuItems = [];

        this.ElementId = "contextMenu";
        this.SetClassnameBackdrop('menuBackDrop');
        this.SetClassnameDialog('contextMenu');
        this.SetClassnameTitle('titleDelete');
        this.SetClassnameCloseButton("hide");
        this.SetClassnameItem("item");

        this.Body = undefined;

        this.Top = top;
        this.Left = left;
    }

    public Add(title : string, action ? : () => any)
    {
        this.MenuItems.push(new ContextMenuItem(title, action));
    }

    protected GenerateElement()
    {
        for(let i = 0; i < this.MenuItems.length; i++)
        {
            let item = this.MenuItems[i];
            item.SetElementClassname(this.ItemClassname);
            this.AppendBody(item.Render());
        }
        let element = super.GenerateElement();
        element.addEventListener("click", this.Remove.bind(this));
        return element;
    }

    public GetMenuItems()
    {
        return this.MenuItems;
    }

    public GetClassnameItem()
    {
        return this.ItemClassname;
    }

    public SetClassnameItem(name : string)
    {
        this.ItemClassname = name;
    }
}