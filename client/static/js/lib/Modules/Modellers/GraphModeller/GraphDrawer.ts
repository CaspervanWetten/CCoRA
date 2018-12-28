/// <reference path='./index.ts'/>

/// <reference path='../Modeller/index.ts'/>

/// <reference path='../../../Models/Store.ts'/>
/// <reference path='../../../Systems/index.ts'/>
/// <reference path='../../../Drawings/index.ts'/>
/// <reference path='../../../HTMLGenerators/Popup/index.ts'/>
/// <reference path='../../../Utils/Datastructures/Pair/Pair.ts'/>

/// <reference path='../../../../vendor/Definitions/Hashtable.d.ts'/>

class GraphDrawer extends Drawer implements SettingsObserver
{
    protected StateDrawings     : IHashtable<number, StateDrawing>
    protected EdgeDrawings      : IHashtable<number, OtherEdgeDrawing>
    protected SelfLoopDrawings  : IHashtable<number, SelfLoopDrawing>

    protected StyleManager      : StyleManager;

    public constructor(width=500, height=500)
    {
        super(width, height);
        this.StateDrawings      = new Hashtable();
        this.EdgeDrawings       = new Hashtable();
        this.SelfLoopDrawings   = new Hashtable();

        this.StyleManager       = new StyleManager();
    }

    public Draw(selected?: number, feedback?: Feedback)
    {
        if(!this.Element) return; // can't draw yet
        // // clear the canvas and draw grid
        this.ClearCanvas();
        this.DrawGrid();

        if(feedback != null) {
            this.StyleManager.SetFeedback(feedback);
        }

        let context = this.Element.getContext('2d');
        let stateDrawings = this.StateDrawings.values();
        let edgeDrawings  = this.EdgeDrawings.values();

        let drawnEdges = new Hashtable<number, Boolean>(); // avoid redrawing
        let seperationDistance = Settings.GetInstance().GetSeperationDistance();
        
        let store = Store.GetInstance();
        // // draw initial state pointer
        if(store.GetGraph().GetInitialState()) {
            context.save();
            context.lineWidth = 2;

            let s = store.GetGraph().GetInitialState();
            let id = s.GetId();
            let drawing = this.StateDrawings.get(id);
            let position = drawing.GetPosition();
            let k = new Point(
                position.X - 40,
                position.Y - 50
            );
            let arrow = new Arrow(k, position);
            arrow.TipHeight = 20;
            arrow.TipWidth = 10;
            arrow.Fill(context);

            context.restore();
        }

        // // draw self loops
        let selfLoopDrawings = this.SelfLoopDrawings.values();
        for(let i = 0; i < selfLoopDrawings.length; i++) {
            context.save();
            let drawing  = selfLoopDrawings[i];
            this.StyleManager.SetSelfLoopStyle(drawing.GetEdge().GetId(), context);
            drawing.Draw(context);
            if(drawing.GetEdge().GetId() == selected) {
                StyleManager.SetSelectedSelfLoopStyle(context);
                drawing.Draw(context);
            }
            StyleManager.SetStandardSelfLoopStyle(context);
            drawing.Draw(context);
            context.restore();
        }

        // draw edges
        for (let i = 0; i < stateDrawings.length; i++) {
            let sd = stateDrawings[i];

            for(let j = 0; j < stateDrawings.length; j++) {
                // get the amount of edges between the two states
                // console.log(edgeDrawings);
                let sharedEdges = edgeDrawings.filter( (edge : OtherEdgeDrawing) => {
                    return edge.GetFromDrawing() == stateDrawings[i] && edge.GetToDrawing() == stateDrawings[j] ||
                           edge.GetFromDrawing() == stateDrawings[j] && edge.GetToDrawing() == stateDrawings[i];
                });
                // draw these edges
                for(let k = 0; k < sharedEdges.length; k++)
                {
                    let edgeDrawing = sharedEdges[k];
                    if(!drawnEdges.get(edgeDrawing.GetEdge().GetId())) {
                        let c = 0;
                        if(sharedEdges.length > 1) {
                            c = (k * seperationDistance) - ((seperationDistance * (sharedEdges.length - 1)) / 2);                            
                        }
                        if (edgeDrawing.GetFromDrawing() == sd) {
                            c = -c;
                        }
                        
                        context.save();
                        edgeDrawing.SetCurvature(c);
                        this.StyleManager.SetEdgeStyle(edgeDrawing.GetEdge().GetId(), context);
                        edgeDrawing.Draw(context);
                        if(edgeDrawing.GetEdge().GetId() == selected){
                            StyleManager.SetSelectedEdgeStyle(context);
                            edgeDrawing.Draw(context);
                        }
                        StyleManager.SetStandardEdgeStyle(context);
                        edgeDrawing.Draw(context) //, edgeDrawing.GetEdge().GetId() == selectedId);
                        drawnEdges.put(edgeDrawing.GetEdge().GetId(), true);
                        context.restore();
                    }
                }
            }
        }

        // draw states
        context.save();
        for(let i = 0; i < stateDrawings.length; i++) {
            let sd = stateDrawings[i];
            this.StyleManager.SetStateStyle(sd.GetState().GetId(), context);            
            sd.Draw(context);
            // StyleManager.SetStandardStateStyle(context);
            if(sd.GetState().GetId() == selected) {
                StyleManager.SetSelectedStateStyle(context);
                sd.Draw(context);
            }
            StyleManager.SetStandardStateStyle(context);
            sd.Draw(context);
        }

        context.restore();
    }

    // protected ProcessFeedback(feedback : Feedback)
    // {
    //     let keys = feedback.SpecificItems.keys();
    //     for(let i = 0; i < keys.length; i++) {
    //         let drawing = this.GetDrawing(keys[i]);
    //         let code = feedback.SpecificItems.get(keys[i]);
    //         drawing.AddFeedback(feedback.GetFeedback(keys[i]));            
    //     }
    // }

    public AddStateDrawing(sd : StateDrawing):  void
    {
        let id = sd.GetState().GetId();
        this.StateDrawings.put(id, sd);
    }

    public RemoveStateDrawing(id : number):     void
    {
        if(this.StateDrawings.containsKey(id)) {
            let drawing = this.StateDrawings.get(id);
            
            let skeys = this.SelfLoopDrawings.keys();
            for(let i = 0; i < skeys.length; i++) {
                let key = skeys[i];
                if (this.SelfLoopDrawings.get(key).GetFromDrawing() == drawing) {
                    this.SelfLoopDrawings.remove(key);
                }
            }

            let ekeys = this.EdgeDrawings.keys();
            for(let i = 0; i < ekeys.length; i++) {
                let key = ekeys[i];
                if(this.EdgeDrawings.get(key).GetFromDrawing() == drawing ||
                   this.EdgeDrawings.get(key).GetToDrawing() == drawing ) {
                       this.EdgeDrawings.remove(key);
                   }
            }
            
            this.StateDrawings.remove(id);
        }
    }

    public GetStateDrawing(id : number):        StateDrawing
    {
        if(this.StateDrawings.containsKey(id)) {
            return this.StateDrawings.get(id);
        }
    }

    public AddEdgeDrawing(d : EdgeDrawing)
    {
        let id = d.GetEdge().GetId();
        let from = d.GetEdge().GetFromState();
        let to   = d.GetEdge().GetToState();
        if(from.equals(to)) {
            this.SelfLoopDrawings.put(id, <SelfLoopDrawing> d);
        }
        else {
            this.EdgeDrawings.put(id, <OtherEdgeDrawing> d);
        }
    }

    public RemoveEdgeDrawing(id : number)
    {
        if(this.SelfLoopDrawings.containsKey(id)) {
            this.SelfLoopDrawings.remove(id);
        }

        if(this.EdgeDrawings.containsKey(id)) {
            this.EdgeDrawings.remove(id);
        }
    }

    public GetEdgeDrawing(id : number):         EdgeDrawing
    {
        if(this.EdgeDrawings.containsKey(id))
        {
            return this.EdgeDrawings.get(id);
        }
        else if(this.SelfLoopDrawings.containsKey(id))
        {
            return this.SelfLoopDrawings.get(id);
        }
    }

    // public GetDrawing(id : number):             GraphElementDrawing
    // {
    //     let res : GraphElementDrawing = this.GetStateDrawing(id);
    //     if(!res) {
    //         res = this.GetEdgeDrawing(id);
    //     }
    //     return res;
    // }

    public GetMoveableDrawing(id : number):     IMoveableDrawing
    {
        let res : IMoveableDrawing;
        if (this.StateDrawings.containsKey(id)) {
            res = this.StateDrawings.get(id);
        }
        if (this.SelfLoopDrawings.containsKey(id)) {
            res = this.SelfLoopDrawings.get(id);
        }
        return res;
    }

    public HitDrawing(position : Point):        number
    {
        if(!this.Element) return;// false;
        let context = this.Element.getContext("2d");

        let sd = this.StateDrawings.keys();
        for(let i = 0; i < sd.length; i++)
        {
            if(this.StateDrawings.get(sd[i]).Hit(context, position)) {
                return sd[i];
            }
        }

        let ekeys = this.EdgeDrawings.keys();
        for(let i = 0; i < ekeys.length; i++) {
            if(this.EdgeDrawings.get(ekeys[i]).Hit(context, position)) {
                return ekeys[i];
            }
        }

        let skeys = this.SelfLoopDrawings.keys();
        for(let i = 0; i < skeys.length; i++) {
            if(this.SelfLoopDrawings.get(skeys[i]).Hit(context, position)) {
                return skeys[i];
            }
        }
        
        return undefined;
    }
    
    public Resize()
    {
        super.Resize();

        let settings = Settings.GetInstance();
        let snap = settings.GetSnapGrid();
        if(snap) {
            let stateDrawings = this.StateDrawings.values();
            for(let i = 0; i < stateDrawings.length; i++)
            {
                let drawing = stateDrawings[i];
                let p = drawing.GetPosition();
                this.SnapPointToGrid(p);
                drawing.MoveTo(p);
            }
        }

        this.Draw();
    }

    public Update(settings : Settings)
    {
        if(this.Element) this.Draw();
    }
    //#region Getters and Setters
    //#endregion
}