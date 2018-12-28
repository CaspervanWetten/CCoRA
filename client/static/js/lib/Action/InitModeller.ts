/// <reference path='./index.ts'/>
/// <reference path='../Modules/index.ts'/>

class InitModeller extends WorkspaceBoundAction<ModellerWorkspace>
{
    public Invoke()
    {
        let split = new SplitContainer();
        let left  = document.createElement("div");
        let rightdrawer = new GraphDrawer(1000, 1000);
        let right = new GraphModeller(rightdrawer);

        let settings = Settings.GetInstance();
        settings.Attach(rightdrawer);
        settings.Attach(right);

        split.SetLeft(left);
        split.SetRight(rightdrawer);

        let store = Store.GetInstance();
        let pid   = store.GetPetrinetId();

        let header = this.GenerateHeader();
        let m      = new Menu();
        let toggle = this.GenerateToggleButton();
        toggle.addEventListener('click', ()=>{m.Toggle()});
        window.addEventListener("keypress", (e)=>{
            if(e.charCode == 77 || e.charCode == 109) {
                m.Toggle();
                toggle.classList.toggle('active');
            }
        });

        let container = this.Workspace.Container;

        container.appendChild(header);
        container.appendChild(m.Render());
        container.appendChild(toggle);
        container.appendChild(split.Render());

        right.GenerateButtons();
        rightdrawer.Resize();
        RequestStation.GetPetrinetImage(new PetrinetImager(left), pid);
        split.Resize();
        right.ToggleTutorial();
    }
    
    protected GenerateHeader()
    {
        let h = document.createElement("header");
        let title = document.createElement("h1");
        title.appendChild(document.createTextNode("CORA"));
        h.appendChild(title);
        return h;
    }

    protected GenerateToggleButton()
    {
        let toggle = document.createElement("div");
        for(let i = 0; i < 3; i++)
        {
            let bar = document.createElement('div');
            bar.classList.add('bar');

            toggle.appendChild(bar);
        }
        toggle.setAttribute("id", "menuToggle");
        toggle.addEventListener('click', ()=>{
            toggle.classList.toggle('active');
        });

        return toggle;
    }

    protected GenerateTutorialPopup()
    {
        let t = new Tutorial(document.body);
        return t.Render();
    }
}