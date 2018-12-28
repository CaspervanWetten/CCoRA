/// <reference path='./index.ts'/>
/// <reference path='../../../../HTMLGenerators/index.ts'/>

class EditEdgeMenu extends GraphModellerMenu
{
    protected Drawing       : EdgeDrawing;
    protected SelectElement : HTMLSelectElement | undefined;

    public constructor(modeller : GraphModeller, edge : EdgeDrawing)
    {
        super(modeller);
        this.Drawing = edge;
    }

    public Focus()
    {
        if(this.Element == null) return;
        this.SelectElement.focus();
    }

    protected GetElement()
    {
        let p = new Popup();

        p.SetCloseable(false);

        let select    = document.createElement("select");
        let petrinet  = Store.GetInstance().GetPetrinet();

        let currentTransition = this.Drawing.GetEdge().GetTransition();

        let transitions = petrinet.GetTransitions().sort();
        for(let i = 0; i < transitions.length; i++)
        {
            if(transitions[i] !== currentTransition) {
                let option = document.createElement("option");
                option.appendChild(document.createTextNode(
                    transitions[i]
                ));
                select.appendChild(option);
            }
        }
        
        select.addEventListener("keypress", (e) => {
            if(e.keyCode == 13) {
                this.Confirm();
            }
        });
        
        let buttonContainer = document.createElement("div");
        let cancelButton = document.createElement("button");
        cancelButton.appendChild(document.createTextNode(
            "Cancel"
        ));
        cancelButton.addEventListener("click", (e)=> {
            this.Hide();
        })
        cancelButton.classList.add("cancel")

        let confirmButton = document.createElement("button");
        confirmButton.appendChild(document.createTextNode(
            "Confirm"
        ));
        confirmButton.addEventListener("click", (e)=> {
            this.Confirm();
        });
        confirmButton.classList.add("confirm");

        buttonContainer.appendChild(cancelButton);
        buttonContainer.appendChild(confirmButton);
        buttonContainer.classList.add("buttons");

        p.SetBody(select);
        p.AppendBody(buttonContainer);
        p.SetElementClassname("edit edge");

        this.SelectElement = select;

        return p.Render();
    }

    protected Confirm()
    {
        let a = new EditEdge(
            this.Modeller.GetDrawer(),
            this.Drawing.GetEdge(),
            this.SelectElement.value
        );
        this.Modeller.ExecuteAndStore(a);
        this.Modeller.GetDrawer().Draw();
        this.Hide();
    }

    protected GetParent()
    {
        return this.Modeller.GetDrawer().GetElement().parentElement;        
    }
}