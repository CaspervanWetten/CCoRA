/// <reference path='./index.ts'/>
/// <reference path='../../../../HTMLGenerators/index.ts'/>

class Tutorial extends HTMLGenerator<HTMLElement>
{
    protected Container : HTMLElement;
    
    public constructor(c : HTMLElement)
    {
        super();
        this.Container = c;
    }

    public Show()
    {
        this.Hide();
        let e = this.Render();
        this.Container.appendChild(e);
    }

    public Hide()
    {
        if(this.Element != null) {
            this.Element.remove();
        }
        this.Element = null;
    }

    public Toggle()
    {
        if(this.Element != null) {
            this.Hide();
        }
        else {
            this.Show();
        }
    }

    protected GenerateElement()
    {
        let popup = new Popup();
        popup.SetCloseable(true);
        popup.SetTitle("Tutorial");
        popup.SetClassnameDialog("popup tutorial");
        let pbody = document.createElement("div");

        let pgen = new ParagraphBuilder();
        pgen.Add(
            "On the left you see an image of the Petri net you selected. This right window is the modeler, which you'll use to model a coverability graph belonging to this Petri net."
        );
        pbody.appendChild(pgen.Render());

        pgen.Clear();
        pgen.Add("You can activate this tutorial again by pressing ");
        pgen.Add(this.GenerateKey("h"));
        pbody.appendChild(pgen.Render());

        let subheader = document.createElement("h2");
        subheader.appendChild(document.createTextNode("Manipulating the Graph"));
        pbody.appendChild(subheader);

        pgen.Clear();
        pgen.Add("You can add states by pressing ");
        pgen.Add(this.GenerateKey("a"));
        pgen.Add(', or by pressing the "Add State" button.');
        pbody.appendChild(pgen.Render());

        pgen.Clear();
        pgen.Add("Select an edge or state by clicking on it.");
        pbody.appendChild(pgen.Render());

        pgen.Clear();
        pgen.Add("Open a context menu by right-clicking.");
        pbody.appendChild(pgen.Render());

        pgen.Clear();
        pgen.Add("Set an initial state by selecting a state and pressing ");
        pgen.Add(this.GenerateKey("i"));
        pgen.Add(".");

        pgen.Clear();
        pgen.Add("You can add an edge between two states by selecting one state and selecting another while holding ");
        pgen.Add(this.GenerateKey("CTRL"));
        pgen.Add(".");
        pbody.appendChild(pgen.Render());

        pgen.Clear();
        pgen.Add("You can edit states by double clicking on them or be pressing ");
        pgen.Add(this.GenerateKey("e"));
        pgen.Add(".");
        pbody.appendChild(pgen.Render());

        pgen.Clear();
        pgen.Add("By pressing ");
        pgen.Add(this.GenerateKey("CTRL-z"));
        pgen.Add(" and ");
        pgen.Add(this.GenerateKey("CTRL-y"));
        pgen.Add(" you can undo and redo your actions");
        pbody.appendChild(pgen.Render());

        pgen.Clear();
        pgen.Add("When you have a state or edge selected, pressing ");
        pgen.Add(this.GenerateKey("DEL"));
        pgen.Add(" deletes this item.");
        pbody.appendChild(pgen.Render());

        popup.SetBody(pbody);
        return popup.Render();
    }

    protected GenerateKey(key : string)
    {
        let span = document.createElement("span");
        span.classList.add("key");
        span.appendChild(document.createTextNode(key));
        return span;
    }
}
