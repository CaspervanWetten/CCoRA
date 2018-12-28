/// <reference path='./index.ts'/>
/// <reference path='../Feedback/index.ts'/>

class FeedbackContainer extends HTMLGenerator<HTMLDivElement>
{
    protected Feedback : Feedback | undefined;
    protected DrawingId : number | undefined;
    protected Container : HTMLElement;

    public constructor(container?: HTMLElement, f ?: Feedback, id ?: number)
    {
        super();
        if(container != null) {
            this.Container = container;
        }
        if(f != null) {
            this.Feedback = f;
        }
        if(id != null) {
            this.DrawingId = id;
        }
        this.ClassPrefix = "FEEDBACK_CONTAINER";
        this.ElementId = "feedbackContainer";
    }
    
    public Display(container ?: HTMLElement)
    {
        if(container != null) {
            this.Container = container;
        }
        if(this.Container == null) {
            return;
        }
        if(this.Element != null) {
            this.Remove();
        }
        let element = this.Render(true);
        this.Container.appendChild(element);
    }

    protected GenerateElement()
    {
        if(this.Feedback == null) return;
        let f = this.Feedback;

        let container = document.createElement("div");
        let g = f.GeneralItems;
        if(g.contains(FeedbackCode.NO_INITIAL_STATE) || g.contains(FeedbackCode.INCORRECT_INITIAL_STATE)) {
            let generalContainer = document.createElement("div");
            generalContainer.classList.add("category");
            let codes = g.values().sort();
            for(let i = 0; i < codes.length; i++) {
                let item = this.RenderGeneralItem(codes[i]);
                generalContainer.appendChild(item);
            }
            container.appendChild(generalContainer);
        }

        let s = f.SpecificItems;
        if(this.DrawingId != null && s.containsKey(this.DrawingId)) {
            let specificContainer = document.createElement("div");
            specificContainer.classList.add("category");
            let record = s.get(this.DrawingId);
            let codes = record.GetCodes();
            codes.sort();
            for(let i = 0; i < codes.length; i++) {
                let item = this.RenderSpecifItem(codes[i]);
                specificContainer.appendChild(item);
            }
            container.appendChild(specificContainer);
        }

        return container;
    }

    protected RenderGeneralItem(code : FeedbackCode)
    {
        let c = document.createElement("div");
        c.classList.add("item");
        let t = FeedbackTranslator.Translate(code);
        c.appendChild(document.createTextNode(t));
        return c;
    }

    protected RenderSpecifItem(code : FeedbackCode)
    {
        let c = document.createElement("div");
        c.classList.add("item");
        let text = FeedbackTranslator.Translate(code);
        c.appendChild(document.createTextNode(text));
        return c;
    }

    
    public SetFeedback(f : Feedback)
    {
        // console.log("setting feedback");
        this.Feedback = f;
        if(this.Element != null) {
            this.Display()
        }
    }

    public SetElementId(id : number)
    {
        this.DrawingId = id;
        if(this.Element != null) {
            this.Display();
        }
    }
}