/// <reference path='./index.ts'/>
/// <reference path='../Feedback/index.ts'/>


class FeedbackButton extends HTMLGenerator<HTMLButtonElement>
{
    protected Feedback : Feedback;

    protected static SubmitText = "Submit";
    protected static BusyText = "Getting Feedback";
    protected static ClearText = "Clear Feedback";

    public constructor()
    {
        super();
        this.ClassPrefix = "FEEDBACK_BUTTON"
    }

    public SetBusy()
    {
        this.SetText(FeedbackButton.BusyText);
    }

    public SetClear()
    {
        this.SetText(FeedbackButton.ClearText);
    }
    
    public SetSubmit()
    {
        this.SetText(FeedbackButton.SubmitText);
    }

    public SetFeedback(f : Feedback)
    {
        this.Feedback = f;
        
        if(f.isEmpty()) {
            this.SetSubmit();
        }
        else {
            this.SetClear();
        }
    }

    protected SetText(s : string)
    {
        if(this.Element != null) {
            this.Element.innerText = s;
        }
    }

    protected GenerateElement()
    {
        let b = document.createElement("button");
        let t = "Submit";
        b.appendChild(document.createTextNode(t));

        this.AddClassname(b, "modeler feedback");

        return b;
    }
}