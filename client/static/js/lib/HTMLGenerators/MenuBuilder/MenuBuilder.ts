/// <reference path='./index.ts'/>
/// <reference path='../../Models/Settings.ts'/>
/// <reference path='../../Utils/Datastructures/Map/Map.ts'/>

class MenuBuilder extends HTMLGenerator<HTMLElement> implements SettingsObserver
{
    public ClassnameOpen        : string;
    public ClassnameClosed      : string;
    public ClassnameCategory    : string;

    protected Categories        : StringMap<MenuCategory>;
    protected static Instance   : MenuBuilder | undefined;

    protected constructor()
    {
        super();
        this.ClassPrefix = "__MENU__";

        this.Categories = {};
        this.ClassnameOpen = "open";
        this.ClassnameClosed = "closed";

        this.ClassnameCategory = "category";
    }

    public static GetInstance()
    {
        if(!MenuBuilder.Instance) {
            MenuBuilder.Instance = new MenuBuilder();
        }
        return MenuBuilder.Instance;
    }

    public Update(settings : Settings)
    {
        let catKeys = Object.keys(this.Categories);
        for(let i = 0; i < catKeys.length; i++) {
            this.Categories[catKeys[i]].Update(settings);
        }
    }

    protected GenerateElement()
    {
        let menu = document.createElement("div");
        this.AddClassname(menu, this.ClassnameClosed);

        let catKeys = Object.keys(this.Categories);
        for(let i = 0; i < catKeys.length; i++) {
            let cat = this.Categories[catKeys[i]];
            menu.appendChild(cat.Render());
        }
        return menu;
    }

    public AddCategory(name : string)
    {
        let cat = new MenuCategory(name);
        if(this.ClassnameCategory) {
            cat.SetElementClassname(this.ClassnameCategory);
        }
        this.Categories[name] = cat;
    }

    public AddMenuItem(category : string, binding : string, item : MenuItem<any>)
    {
        if(!this.Categories[category]) {
            this.AddCategory(category);
        }
        this.Categories[category].AddItem(binding, item);
    }

    public Open()
    {
        let element = this.Element;
        element.classList.remove(this.ClassnameClosed);
        element.classList.add(this.ClassnameOpen);
    }

    public Close()
    {
        let element = this.Element;
        element.classList.remove(this.ClassnameOpen);
        element.classList.add(this.ClassnameClosed);
    }

    public Toggle()
    {
        let element = this.Element;
        if(element.classList.contains(this.ClassnameOpen)){
            this.Close();
        } else {
            this.Open();
        }
    }
}