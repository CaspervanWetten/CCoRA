/// <reference path='./index.ts'/>
/// <reference path='../index.ts'/>

/// <reference path='../../Converters/index.ts'/>
/// <reference path='../../../Drawings/index.ts'/>

/// <reference path='../../../Utils/Datastructures/Point/Point.ts'/>
/// <reference path='../../../Utils/Datastructures/Size/Size.ts'/>
/// <reference path='../../../Utils/Datastructures/Stack/Stack.ts'/>

class GraphModeller extends Modeller<GraphDrawer> implements IResponseInterpreter, SettingsObserver
{
    protected InitialPoint : Point | undefined;
    protected MouseOffset  : Size  | undefined;
    protected MousePos     : Point | undefined;

    public    CurrentMenu  : GraphModellerMenu | undefined;
    public    SelectedId   : number | undefined;
    public    HoveredId    : number | undefined;

    public    Feedback     : Feedback | undefined;

    protected History : HistoryList<GraphModellerAction>;

    protected AddStateButton : HTMLButtonElement;
    protected FeedbackButton : FeedbackButton;
    protected FeedbackContainer : FeedbackContainer;
    protected Tutorial       : Tutorial;

    protected SubInterpreters : IResponseInterpreter[];

    public constructor(drawer : GraphDrawer)
    {
        super(drawer);
        this.InitialPoint = undefined;
        this.MouseOffset  = undefined;
        this.MousePos     = undefined;

        this.CurrentMenu = undefined;
        this.SelectedId  = undefined;

        this.History = new HistoryList();

        this.FeedbackContainer = new FeedbackContainer(this.Drawer.GetElement().parentElement);
        this.Tutorial = new Tutorial(document.body);
    }
    
    public Register()
    {
        super.Register();
        let modeller = this.Drawer;
        let element = modeller.GetElement();

        window.addEventListener('keydown', (e)=>{
            this.KeyPress(e);
        });

        element.addEventListener("contextmenu", (e)=>{
            this.RightClick(e);
        });

        element.addEventListener("mousedown", (e)=>{
            this.MouseDown(e);
        });

        element.addEventListener("mousemove", (e)=>{
            this.MouseMove(e);
        });

        element.addEventListener("mouseup", (e)=> {
            this.MouseUp(e);
        });

        element.addEventListener("dblclick", (e)=> {
            this.DoubleClick(e);
        });
    }

    
    public GenerateButtons()
    {
        let addStateButton = new AddStateButton();
        let button = addStateButton.Render();
        button.addEventListener("click", (e) => {
            this.AddState();
        });

        let p = this.Drawer.GetElement().parentElement;
        p.appendChild(button);

        this.AddStateButton = button;
        
        let feedbackButton = new FeedbackButton();
        button = feedbackButton.Render();
        button.addEventListener("click", (e)=> {
            if(this.Feedback.isEmpty()) {
                this.GetFeedback();
            }
            else {
                this.ClearFeedback();
            }
        });

        p.appendChild(button);

        this.FeedbackButton = feedbackButton;
        this.Update(Settings.GetInstance());
    }

    public ExecuteAndStore(action : GraphModellerAction)
    {
        action.Invoke();
        this.History.Add(action);

        let settings = Settings.GetInstance();
        if(settings.GetDifficulty() == ModelingDifficulty.NOVICE){
            this.GetFeedback();
        } else if(settings.GetDifficulty() == ModelingDifficulty.ADVANCED) {
            this.ClearFeedback();
        }
    }
    
    public Undo()
    {
        if(!this.History.IsEmpty()) {
            this.History.Undo();

            let settings = Settings.GetInstance();
            if(settings.GetDifficulty() == ModelingDifficulty.NOVICE){
                this.GetFeedback();
            } else if (settings.GetDifficulty() == ModelingDifficulty.ADVANCED) {
                this.ClearFeedback();
            }
            this.DrawGraph();
        }
    }

    public Redo()
    {
        this.History.Redo();
        let settings = Settings.GetInstance();
        if(settings.GetDifficulty() == ModelingDifficulty.NOVICE){
            this.GetFeedback();
        } else if (settings.GetDifficulty() == ModelingDifficulty.ADVANCED) {
            this.ClearFeedback();
        }
        this.DrawGraph();
    }
    
    public DrawGraph()
    {
        this.Drawer.Draw(this.SelectedId, this.Feedback);
    }

    //#region graph manipulation
    public AddState(position = undefined)
    {
        let action = new AddState(this.Drawer, "", position);
        this.ExecuteAndStore(action);
        this.DrawGraph();
        this.HideMenu();
    }

    public SetInitial()
    {
        let graph = Store.GetInstance().GetGraph();
        let initial = graph.GetInitialState();

        // null checks to prevent duplicate initial state setters
        if((this.SelectedId != null && initial == null && graph.ContainsState(this.SelectedId)) ||
           (initial != null && this.SelectedId != null && initial.GetId() != this.SelectedId && graph.ContainsState(this.SelectedId)) ||
           (initial != null && this.SelectedId == null)) {
            let a = new SetInitialState(this.Drawer, this.SelectedId);
            this.ExecuteAndStore(a);
            this.DrawGraph();
        }
        this.HideMenu();
    }

    public RemoveElement()
    {
        if(this.SelectedId == null) return;
        let id = this.SelectedId;
        let graph = Store.GetInstance().GetGraph();

        let a : GraphModellerAction;
        if(graph.ContainsEdge(id)) {
            a = new RemoveEdge(this.Drawer, id);
        }
        else if(graph.ContainsState(id)) {
            a = new RemoveState(this.Drawer, id);
        }

        this.ExecuteAndStore(a);
        this.DrawGraph();

        this.SelectedId = null;
        this.HideMenu();
    }
    //#endregion
    
    //#region SettingsObserver
    // Update on Settings Change
    public Update(s : Settings)
    {
        let diff = s.GetDifficulty();
        if (diff == ModelingDifficulty.NOVICE) {
            this.GetFeedback();
            if(this.FeedbackButton != null) {
                this.FeedbackButton.GetElement().classList.add("disabled");
            }
        }
        else if(diff == ModelingDifficulty.ADVANCED) {
            this.ClearFeedback();
            if(this.FeedbackButton != null) {
                this.FeedbackButton.GetElement().classList.remove("disabled");
            }
        }
    }
    //endregion
  
    //#region Feedback
    public ClearFeedback()
    {
        this.Feedback = new Feedback();
        this.FeedbackContainer.SetFeedback(this.Feedback);
        this.FeedbackButton.SetFeedback(this.Feedback);
        this.DrawGraph();
    }

    public GetFeedback()
    {
        let graph = Store.GetInstance().GetGraph();
        if(!graph.IsEmpty()) {
            let graphstring = new GraphToJson(graph).Convert();
            RequestStation.GetFeedback(this, graph);
        }
    }

    public ReceiveBusy() {
        if(this.FeedbackButton != null) {
            let button = this.FeedbackButton;
            button.SetBusy();
        }
    }

    public ReceiveSuccess(code : number, responseText : string) {
        try{
            this.Feedback = Feedback.JsonToFeedback(responseText);
            this.FeedbackContainer.SetFeedback(this.Feedback);
            if(Settings.GetInstance().GetDifficulty() == ModelingDifficulty.NOVICE) {
                this.FeedbackContainer.SetFeedback(this.Feedback);
                this.FeedbackContainer.Display(this.Drawer.GetElement().parentElement);
            }
            else {
                this.FeedbackButton.SetFeedback(this.Feedback);
            }
            this.DrawGraph();
        }
        catch(e) 
        {
            console.log("could not parse feedback");
        }
    }

    public ReceiveFailure(code, responseText : string) {
        try{
            let e = JSON.parse(responseText);
            console.log(e);
        }
        catch(ex) {
            console.log(responseText);
        }
    }

    protected PrintFeedback()
    {
        let f = this.Feedback;
        let general = f.GeneralItems.values();
        console.log(general);
        let skeys = f.SpecificItems.keys().sort();
        for(let i = 0; i < skeys.length; i++) {
            let id = skeys[i];
            let codes = f.SpecificItems.get(id).Items.values();
            console.log(id, codes);
        }
    }

    public Attach(irp : IResponseInterpreter)
    {
        this.SubInterpreters.push(irp);
    }

    public Detach(irp : IResponseInterpreter)
    {
        let index = this.SubInterpreters.indexOf(irp);
        if(index >= 0) {
            this.SubInterpreters.removeAt(this.SubInterpreters.indexOf(irp));
        }
    }
    //endregion

    //#region Events
    protected MouseDown(e : MouseEvent)
    {
        let petrinet = Store.GetInstance().GetPetrinet();

        this.InitialPoint = this.Drawer.TranslatePointFromViewportToCanvas(
            new Point(e.offsetX, e.offsetY)
        );
        if(!e.ctrlKey) {
            this.SelectedId = this.Drawer.HitDrawing(this.InitialPoint);
            let drawing = this.GetSelectedDrawing();
            if(drawing != null && drawing instanceof StateDrawing) {
                this.MouseOffset = new Size(
                    this.InitialPoint.X - drawing.GetPosition().X,
                    this.InitialPoint.Y - drawing.GetPosition().Y
                );
            }
        }
        else if(this.SelectedId != null) {
            let transitions = petrinet.GetTransitions().sort();
            let t0 = transitions[0];
            let otherId = this.Drawer.HitDrawing(this.InitialPoint);
            if(otherId != null) {
                let a = new AddEdge(this.Drawer, this.SelectedId, otherId, t0);
                this.ExecuteAndStore(a);
            }
        }
    }

    protected MouseMove(e : MouseEvent)
    {
        this.MousePos = new Point(e.offsetX, e.offsetY);

        let p = new Point(e.offsetX, e.offsetY);
        p = this.Drawer.TranslatePointFromViewportToCanvas(p);
        let k = this.Drawer.HitDrawing(p);
        if(k != null) {
            this.FeedbackContainer.SetElementId(k);
        } else if (
            this.Feedback != null && 
            !this.Feedback.GeneralItems.contains(FeedbackCode.INCORRECT_INITIAL_STATE) && 
            !this.Feedback.GeneralItems.contains(FeedbackCode.NO_INITIAL_STATE)) {
            this.FeedbackContainer.Remove();
        }

        if(this.MouseIsDown && this.InitialPoint && !e.ctrlKey) {
            let drawing = this.GetSelectedDrawing();

            let p = this.Drawer.TranslatePointFromViewportToCanvas(
                new Point(e.offsetX, e.offsetY)
            );
            
            if(drawing != null) {
                if(this.MouseOffset) {
                    p.X -= this.MouseOffset.Width;
                    p.Y -= this.MouseOffset.Height;
                }
                if(Settings.GetInstance().GetSnapGrid() && drawing instanceof StateDrawing) {
                    p = this.Drawer.SnapPointToGrid(p);
                }    
                drawing.MoveTo(p);
                this.DrawGraph();
            }
        }
    }

    protected RightClick(e : MouseEvent)
    {
        e.preventDefault();
        // simulate left click
        this.MouseDown(e);
        this.MouseUp(e);
        let position = new Point(e.pageX, e.pageY);
        this.ShowContextMenu(position, new Point(e.offsetX, e.offsetY));
        this.MouseIsDown = false;
    }

    protected DoubleClick(e : MouseEvent)
    {
        this.EditElementMenu();
    }

    protected MouseUp(e : MouseEvent)
    {
        this.InitialPoint = undefined;
        this.MouseOffset = undefined;
        this.DrawGraph();
    }

    protected KeyPress(e : KeyboardEvent)
    {   
        switch(e.keyCode) {
            case 72: 
            {
                this.ToggleTutorial();
            }
        }
        // if a menu is not open
        if(this.LastDownTarget == this.Drawer.GetElement() && !this.CurrentMenu) {
            switch(e.keyCode) {
                case 46: // delete
                {
                    this.RemoveElement();
                    break;
                }
                case 65: // a
                {
                    let p = this.MousePos;
                    let action = new AddState(this.Drawer, "", p);
                    this.ExecuteAndStore(action);
                    this.DrawGraph();
                    break;
                }
                case 69: //e
                {
                    e.preventDefault();
                    this.EditElementMenu();
                    break;
                }
                case 73: // i
                {
                    this.SetInitial();
                    break;
                }
                case 89: // y
                {
                    if(e.ctrlKey)
                    {
                        this.Redo();
                    }
                    break;
                }
                case 90: // z
                {
                    if(e.ctrlKey)
                    {
                        if(!e.shiftKey) {
                            this.Undo();
                        }
                        else {
                            this.Redo();
                        }
                    }
                    break;
                }
            }
        }
        else if (this.LastDownTarget == this.Drawer.GetElement() && this.CurrentMenu != null) {
            switch(e.keyCode)
            {
                case 27: // esc
                {
                    this.HideMenu();
                    break;
                }
            }
        }
    }

    //#endregion

    //#region Menus
    public ShowMenu(menu : GraphModellerMenu)
    {
        this.HideMenu(); // hide the current menu
        this.CurrentMenu = menu;
        this.CurrentMenu.Show();
        this.CurrentMenu.Focus();
    }

    public HideMenu()
    {
        if(this.CurrentMenu) {
            this.CurrentMenu.Remove();
            this.CurrentMenu = undefined;

            if(this.Drawer.GetElement() != null)
            {
                this.LastDownTarget = this.Drawer.GetElement()
            }
        }
    }
    
    public EditElementMenu()
    {
        if(this.SelectedId != null) {
            let sd = this.Drawer.GetStateDrawing(this.SelectedId);
            if(sd != null) {
                this.ShowEditStateMenu(sd);
                return;
            }
            let ed = this.Drawer.GetEdgeDrawing(this.SelectedId);
            if(ed != null) {
                this.ShowEditEdgeMenu(ed);
            }
        }
    }

    public ToggleTutorial()
    {
        // let t = new Tutorial(document.body);
        let t = this.Tutorial;
        t.Toggle();
    }
    
    protected ShowEditStateMenu(sd: StateDrawing)
    {
        let menu = new EditStateMenu(this, sd);
        this.ShowMenu(menu);
        menu.SetLeft();
        menu.SetTop();
    }

    protected ShowEditEdgeMenu(ed : EdgeDrawing)
    {
        let menu = new EditEdgeMenu(this, ed);
        this.ShowMenu(menu);
    }

    protected ShowContextMenu(p : Point, rp?:Point)
    {
        let menu = new GraphModellerContextMenu(this, p, rp);
        this.ShowMenu(menu);
    }

    //#endregion

    //#region Helpers
    protected GetSelectedDrawing()
    {
        if(this.SelectedId != null)
            return this.Drawer.GetMoveableDrawing(this.SelectedId);
    }
    //#endregion

    public GetDrawer()
    {
        return this.Drawer;
    }
}