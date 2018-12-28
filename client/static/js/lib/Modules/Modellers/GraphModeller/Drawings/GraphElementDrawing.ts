/// <reference path='./index.ts'/>
/// <reference path='../index.ts'/>

/// <reference path='../../../../Drawings/index.ts'/>
/// <reference path='../../../../../vendor/Definitions/Hashtable.d.ts'/>

abstract class GraphElementDrawing extends Drawing
{
    protected Drawer    : GraphDrawer;
    // protected Feedback  : FeedbackRecord | undefined;

    public constructor(drawer : GraphDrawer)
    {
        super();
        this.Drawer = drawer;
        // this.Feedback = undefined
    }

    // public AddFeedback(fb : FeedbackRecord)
    // {
    //     this.Feedback = fb;
    // }

    // public PrintFeedback()
    // {
    //     if(this.Feedback != null && !this.Feedback.IsEmpty()) {
    //         console.log(this.Feedback.ToStrings());
    //     }
    // }
}