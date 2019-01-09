/// <reference path='./index.ts'/>
/// <reference path='../Feedback/index.ts'/>
/// <reference path='../../../../../vendor/Definitions/Hashtable.d.ts'/>

class StyleManager
{
    protected Feedback  : Feedback | undefined;
    protected Callbacks : IHashtable<FeedbackCode, StyleManagerAction>;

    public constructor()
    {
        this.Feedback = undefined;
        this.Callbacks = new Hashtable();

        let c = this.Callbacks;
        // states
        c.put(FeedbackCode.NOT_REACHABLE_FROM_PRESET, new SetIncorrectStateStyle());
        c.put(FeedbackCode.REACHABLE_FROM_PRESET, new SetCorrectStateStyle());
        c.put(FeedbackCode.EDGE_MISSING, new SetWarningStateStyle());
        c.put(FeedbackCode.DUPLICATE_STATE, new SetWarningStateStyle());
        c.put(FeedbackCode.OMEGA_OMITTED, new SetWarningStateStyle());
        c.put(FeedbackCode.NOT_REACHABLE_FROM_INITIAL, new SetWarningStateStyle());

        // edges
        c.put(FeedbackCode.ENABLED_CORRECT_POST, new SetCorrectEdgeStyle());
        c.put(FeedbackCode.ENABLED_CORRECT_POST_WRONG_LABEL, new SetIncorrectEdgeStyle());
        c.put(FeedbackCode.ENABLED_INCORRECT_POST, new SetIncorrectEdgeStyle());
        c.put(FeedbackCode.DISABLED, new SetIncorrectEdgeStyle());
        c.put(FeedbackCode.DISABLED_CORRECT_POST, new SetIncorrectEdgeStyle());
        c.put(FeedbackCode.DUPLICATE_EDGE, new SetWarningEdgeStyle());
        c.put(FeedbackCode.MISSED_SELF_LOOP, new SetIncorrectEdgeStyle());
    }

    //#region Standard Styles
    // shape styles - normal
    public static SetStandardStateStyle(context : CanvasRenderingContext2D)
    {
        context.fillStyle = "white";
        context.strokeStyle = "black";
        context.lineWidth = 2;
    }

    public static SetStandardEdgeStyle(context : CanvasRenderingContext2D)
    {
        context.fillStyle = "white";
        context.strokeStyle = "black";
        context.lineWidth = 2;
    }
    public static SetStandardSelfLoopStyle(context : CanvasRenderingContext2D)
    {
        context.fillStyle = "white";
        context.strokeStyle = "black";
        context.lineWidth = 2;
    }
    // shape styles - selected
    public static SetSelectedStateStyle(context : CanvasRenderingContext2D)
    {
        context.fillStyle = "white";
        context.strokeStyle = "#7ad3ff99";
        context.lineWidth = 20;
    }

    public static SetSelectedEdgeStyle(context : CanvasRenderingContext2D)
    {
        context.fillStyle = "white";
        context.strokeStyle = "#7ad3ff99";
        context.lineWidth = 20;
    }

    public static SetSelectedSelfLoopStyle(context : CanvasRenderingContext2D)
    {
        context.fillStyle = "transparent";
        context.strokeStyle = "#7ad3ff99";
        context.lineWidth = 20;
    }
    // text styles
    public static SetTextStyleState(context : CanvasRenderingContext2D)
    {
        context.fillStyle = "black";
        context.font = "14pt monospace";
        context.textBaseline = "middle";
        context.textAlign = "center";
    }

    public static SetTextStyleEdge(context : CanvasRenderingContext2D)
    {
        context.fillStyle = "black";
        context.font = "13pt monospace";
        context.textBaseline = "middle";
        context.textAlign = "center";
    }

    public static SetTextStyleSelfLoop(context : CanvasRenderingContext2D)
    {
        context.fillStyle = "black";
        context.font = "13pt monospace";
        context.textBaseline = "middle";
        context.textAlign = "center";
    }
    //#endregion


    //#region adding feedback
    public SetFeedback(feedback : Feedback) {
        this.Feedback = feedback;
    }

    public ClearFeedback()
    {
        this.Feedback = undefined;
    }
    //#endregion


    //#region Setting non-standard styles
    public SetStateStyle(id : number, context : CanvasRenderingContext2D)
    {
        if(this.Feedback != null && this.Feedback.Contains(id)) {
            let f = this.Feedback.GetFeedback(id);
            let codes = f.GetCodes();
            let k = codes as number[];
            let c = Math.max(...k);
            if(this.Callbacks.containsKey(c))
                this.Callbacks.get(c).Invoke(context);
            else
                StyleManager.SetStandardStateStyle(context);
        }
        else{
            StyleManager.SetStandardStateStyle(context);
        }
    }

    public SetEdgeStyle(id : number, context : CanvasRenderingContext2D)
    {
        if(this.Feedback != null && this.Feedback.Contains(id)) {
            let f = this.Feedback.GetFeedback(id);
            let codes = f.GetCodes();
            let k = codes as number[];
            let c = Math.max(...k);
            if(this.Callbacks.containsKey(c)) {
                this.Callbacks.get(c).Invoke(context);
            }
            else {
                StyleManager.SetStandardEdgeStyle(context);
            }
        }
        else {
            StyleManager.SetStandardEdgeStyle(context);
        }
    }

    public SetSelfLoopStyle(id : number, context : CanvasRenderingContext2D)
    {
        if(this.Feedback != null && this.Feedback.Contains(id)) {
            let f = this.Feedback.GetFeedback(id);
            let codes = f.GetCodes();
            let k = codes as number[];
            let c = Math.max(...k);
            if(this.Callbacks.containsKey(c)) {
                this.Callbacks.get(c).Invoke(context);
            } 
            else {
                StyleManager.SetStandardSelfLoopStyle(context);
            }
        }
        else {
            StyleManager.SetStandardSelfLoopStyle(context);
        }
    }
    //#endregion
}
